<?php
session_start();
require_once __DIR__ . "/includes/database_connect.php";
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["reply" => "Invalid request"]);
    exit;
}

$user_message = trim($_POST['message'] ?? '');

if ($user_message === '') {
    echo json_encode(["reply" => "Please type something 😊"]);
    exit;
}

// --------- Helper: Language Detection ----------
function detectLanguage($text) {
    $url = "https://translate.googleapis.com/translate_a/single?client=gtx&sl=auto&tl=en&dt=t&q=" . urlencode($text);
    $response = @file_get_contents($url);
    if (!$response) {
        return 'en';
    }
    $data = json_decode($response, true);
    return $data[2] ?? 'en';
}

// --------- Helper: Translation ----------
function translateText($text, $from, $to) {
    if ($from === $to) {
        return $text;
    }
    $url = "https://translate.googleapis.com/translate_a/single?client=gtx&sl=$from&tl=$to&dt=t&q=" . urlencode($text);
    $response = @file_get_contents($url);
    if (!$response) {
        return $text;
    }
    $data = json_decode($response, true);
    return $data[0][0][0] ?? $text;
}

// --------- Helper: Extract budget (first number) ----------
function extractBudget($text) {
    if (preg_match_all('/\d+/', $text, $matches)) {
        if (!empty($matches[0])) {
            return (int)$matches[0][0];
        }
    }
    return null;
}

// --------- Helper: Detect city from text & DB ----------
function detectCityFromText($con, $englishText) {
    $detected = [
        'city_id' => null,
        'city_name' => null,
    ];

    if (!$con) {
        return $detected;
    }

    $englishText = strtolower($englishText);

    // Handle Bangalore / Bengaluru synonym
    if (strpos($englishText, 'bangalore') !== false) {
        $englishText .= ' bengaluru';
    }

    $cities_res = mysqli_query($con, "SELECT id, name FROM cities");
    if ($cities_res) {
        while ($row = mysqli_fetch_assoc($cities_res)) {
            $name = strtolower($row['name']);
            if (strpos($englishText, $name) !== false) {
                $detected['city_id'] = (int)$row['id'];
                $detected['city_name'] = $row['name'];
                break;
            }
        }
    }

    return $detected;
}

// --------- Helper: Recommend PGs from DB (sorted by rating DESC, then rent ASC) ----------
function getRecommendations($con, $cityId = null, $budget = null) {
    if (!$con) {
        return [];
    }

    $conditions = [];
    if ($cityId !== null) {
        $conditions[] = "p.city_id = " . (int)$cityId;
    }
    if ($budget !== null && $budget > 0) {
        $conditions[] = "p.rent <= " . (int)$budget;
    }

    $where = "";
    if (!empty($conditions)) {
        $where = "WHERE " . implode(" AND ", $conditions);
    }

    $sql = "
        SELECT 
            p.*,
            (p.rating_clean + p.rating_food + p.rating_safety) / 3 AS avg_rating,
            c.name AS city_name
        FROM properties p
        INNER JOIN cities c ON p.city_id = c.id
        $where
        ORDER BY avg_rating DESC, p.rent ASC
        LIMIT 3
    ";

    $result = mysqli_query($con, $sql);
    $rows = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
    }
    return $rows;
}

// --------- Helper: FAQ lookup from chatbot_faq ----------
function findFaqAnswer($con, $englishText) {
    if (!$con) {
        return null;
    }
    $englishText = strtolower($englishText);
    $res = mysqli_query($con, "SELECT keyword, answer FROM chatbot_faq");
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $kw = strtolower($row['keyword']);
            if ($kw !== '' && strpos($englishText, $kw) !== false) {
                return $row['answer']; // will be translated later
            }
        }
    }
    return null;
}

// --------- Conversation context in session ----------
if (!isset($_SESSION['chatbot_ctx']) || !is_array($_SESSION['chatbot_ctx'])) {
    $_SESSION['chatbot_ctx'] = [];
}
$ctx = &$_SESSION['chatbot_ctx'];

$lang = detectLanguage($user_message);
$englishText = strtolower(translateText($user_message, $lang, "en"));

$reply_en = "";
$intent = "general";

// 1) FAQ priority
$faqAnswer = findFaqAnswer($con, $englishText);
if ($faqAnswer !== null) {
    $reply_en = $faqAnswer;
    $intent = "faq";
}
// 2) Booking flow handling
elseif (strpos($englishText, 'book') !== false || strpos($englishText, 'booking') !== false) {
    // Start booking conversation
    $ctx = [
        'mode' => 'booking',
        'step' => 1,
        'city_id' => null,
        'city_name' => null,
        'budget' => null,
        'gender' => null,
    ];
    $reply_en = "Sure, I can help you with PG booking. First, please tell me the city (for example Delhi, Mumbai, Bengaluru or Hyderabad).";
    $intent = "booking_start";
}
elseif (isset($ctx['mode']) && $ctx['mode'] === 'booking') {
    $intent = "booking_flow";
    $step = $ctx['step'] ?? 1;

    if ($step === 1) {
        // try to detect city
        $det = detectCityFromText($con, $englishText);
        if ($det['city_id'] !== null) {
            $ctx['city_id'] = $det['city_id'];
            $ctx['city_name'] = $det['city_name'];
            $ctx['step'] = 2;
            $reply_en = "Great, city set to " . $det['city_name'] . ". What is your maximum monthly budget (in rupees)?";
        } else {
            $reply_en = "Please mention the city name clearly (for example Delhi, Mumbai, Bengaluru or Hyderabad).";
        }
    } elseif ($step === 2) {
        $budget = extractBudget($englishText);
        if ($budget !== null) {
            $ctx['budget'] = $budget;
            $ctx['step'] = 3;
            $reply_en = "Okay, budget considered around ₹" . $budget . " per month. Do you prefer Boys, Girls or Unisex PG?";
        } else {
            $reply_en = "Please tell me your budget as a number, for example 8000 or 10000.";
        }
    } elseif ($step === 3) {
        $gender = null;
        if (strpos($englishText, 'boy') !== false || strpos($englishText, 'male') !== false || strpos($englishText, 'gents') !== false) {
            $gender = 'male';
        } elseif (strpos($englishText, 'girl') !== false || strpos($englishText, 'female') !== false || strpos($englishText, 'ladies') !== false) {
            $gender = 'female';
        } elseif (strpos($englishText, 'unisex') !== false || strpos($englishText, 'any') !== false) {
            $gender = 'unisex';
        }

        if ($gender === null) {
            $reply_en = "Please specify Boys, Girls or Unisex PG.";
        } else {
            $ctx['gender'] = $gender;
            // Final recommendation step
            $recs = getRecommendations($con, $ctx['city_id'], $ctx['budget']);
            if (!empty($recs)) {
                $lines = [];
                foreach ($recs as $pg) {
                    $avg = isset($pg['avg_rating']) ? round($pg['avg_rating'], 1) : 0;
                    $lines[] = $pg['name'] . " – ₹" . $pg['rent'] . " – Rating " . $avg . "/5 in " . $pg['city_name'];
                }
                $reply_en = "Based on your preferences, here are some options:
- " . implode("
- ", $lines) . "
You can open the PG List / property pages on the website to see full details and proceed with booking.";
            } else {
                $reply_en = "I could not find PGs that exactly match your filters. You can try increasing your budget or changing the city filters on the PG List page.";
            }
            // reset booking context after giving suggestions
            $ctx = [];
        }
    } else {
        $reply_en = "You can start again by saying 'help me book a PG'.";
        $ctx = [];
    }
}
// 3) Direct recommendation (no booking flow) - user asks PG with city / budget
elseif (strpos($englishText, 'pg') !== false || strpos($englishText, 'room') !== false || strpos($englishText, 'hostel') !== false) {
    $intent = "recommendation";
    $det = detectCityFromText($con, $englishText);
    $budget = extractBudget($englishText);
    $recs = getRecommendations($con, $det['city_id'], $budget);

    if (!empty($recs)) {
        $intro = "Here are some recommended PGs";
        if (!empty($det['city_name'])) {
            $intro .= " in " . $det['city_name'];
        }
        if ($budget !== null) {
            $intro .= " under about ₹" . $budget;
        }
        $intro .= ":";

        $lines = [];
        foreach ($recs as $pg) {
            $avg = isset($pg['avg_rating']) ? round($pg['avg_rating'], 1) : 0;
            $lines[] = $pg['name'] . " – ₹" . $pg['rent'] . " – Rating " . $avg . "/5 in " . $pg['city_name'];
        }
        $reply_en = $intro . "\n- " . implode("\n- ", $lines) . "\nYou can visit the PG List page to see photos and full details.";
    } else {
        $reply_en = "I couldn't find an exact match. Try specifying the city and your budget, for example: 'PG in Delhi under 8000'.";
    }
}
// 4) Generic rule-based answers (fallback)
else {
    if (strpos($englishText, 'rent') !== false || strpos($englishText, 'price') !== false || strpos($englishText, 'budget') !== false) {
        $reply_en = "You can use the rent/budget filter on the PG List page to see PGs within your price range.";
    } elseif (strpos($englishText, 'safety') !== false || strpos($englishText, 'secure') !== false) {
        $reply_en = "Safety is measured using ratings like cleanliness, food and safety. You can also check user reviews on each PG detail page.";
    } elseif (strpos($englishText, 'facility') !== false || strpos($englishText, 'amenities') !== false || strpos($englishText, 'wifi') !== false) {
        $reply_en = "Facilities such as WiFi, food, laundry, parking, AC etc. are listed in the amenities section of each PG detail page.";
    } elseif (strpos($englishText, 'hello') !== false || strpos($englishText, 'hi') !== false) {
        $reply_en = "Hello! I am your AI PG Assistant. You can ask me about PGs, rent, safety, facilities or booking.";
    } elseif (strpos($englishText, 'login') !== false || strpos($englishText, 'signup') !== false || strpos($englishText, 'register') !== false) {
        $reply_en = "Use the Login / Signup buttons on the top bar of the website to create your account and access more features.";
    } else {
        $reply_en = "I can help you with PG search, rent, facilities, safety and booking. Try asking something like: 'Suggest a PG in Delhi under 9000'.";
    }
}

// Translate back to user's language
$final_reply = translateText($reply_en, "en", $lang);

// Save chat to database (simple log)
if ($con) {
    mysqli_query($con, "CREATE TABLE IF NOT EXISTS chat_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NULL,
        user_message TEXT,
        bot_reply TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $u = $_SESSION['user_id'] ?? null;
    $stmt = mysqli_prepare($con, "INSERT INTO chat_logs (user_id, user_message, bot_reply) VALUES (?, ?, ?)");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "iss", $u, $user_message, $final_reply);
        mysqli_stmt_execute($stmt);
    }
}

echo json_encode(["reply" => $final_reply]);
