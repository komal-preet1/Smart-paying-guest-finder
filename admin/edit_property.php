<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db_connect.php';

$id = (isset($_GET['id']) && (int)$_GET['id'] > 0) ? (int)$_GET['id'] : 0;

/* DELETE IMAGE */
if(isset($_GET['delete_img'])) {
    $img_id = (int)$_GET['delete_img'];

    $stmt = $pdo->prepare("SELECT image_path FROM property_images WHERE id=?");
    $stmt->execute([$img_id]);
    $img = $stmt->fetch(PDO::FETCH_ASSOC);

    if($img){
        $file = __DIR__ . "/../" . $img['image_path'];
        if(file_exists($file)) unlink($file);
        $pdo->prepare("DELETE FROM property_images WHERE id=?")->execute([$img_id]);
    }
    header("Location: edit_property.php?id=".$id);
    exit;
}

/* FETCH PROPERTY */
$prop=[];
if($id>0){
    $st=$pdo->prepare("SELECT * FROM properties WHERE id=?");
    $st->execute([$id]);
    $prop=$st->fetch(PDO::FETCH_ASSOC) ?: [];
}

/* FETCH AMENITIES */
$allAmenities=$pdo->query("SELECT id,name FROM amenities")->fetchAll(PDO::FETCH_ASSOC);

$selectedAmenities=[];
if($id>0){
    $st=$pdo->prepare("SELECT amenity_id FROM properties_amenities WHERE property_id=?");
    $st->execute([$id]);
    $selectedAmenities=$st->fetchAll(PDO::FETCH_COLUMN);
}

/* SAVE FORM */
if($_SERVER['REQUEST_METHOD']==='POST'){

    $city_id = isset($_POST['city_id']) ? (int)$_POST['city_id'] : 0;
    if($city_id==0){ die("Please select a valid city."); }

    $name = $_POST['name'] ?? '';
    $address = $_POST['address'] ?? '';
    $description = $_POST['description'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $rent = (int)($_POST['rent'] ?? 0);
    $clean = $_POST['rating_clean'] ?? 0;
    $food = $_POST['rating_food'] ?? 0;
    $safety = $_POST['rating_safety'] ?? 0;

    if($id>0){
        $pdo->prepare("UPDATE properties 
        SET city_id=?,name=?,address=?,description=?,gender=?,rent=?,rating_clean=?,rating_food=?,rating_safety=? 
        WHERE id=?")
        ->execute([$city_id,$name,$address,$description,$gender,$rent,$clean,$food,$safety,$id]);
        $property_id=$id;
    } else {
        $pdo->prepare("INSERT INTO properties(city_id,name,address,description,gender,rent,rating_clean,rating_food,rating_safety)
        VALUES(?,?,?,?,?,?,?,?,?)")
        ->execute([$city_id,$name,$address,$description,$gender,$rent,$clean,$food,$safety]);
        $property_id=$pdo->lastInsertId();
    }

    /* AMENITIES */
    $pdo->prepare("DELETE FROM properties_amenities WHERE property_id=?")->execute([$property_id]);
    if(!empty($_POST['amenities'])){
        $st=$pdo->prepare("INSERT INTO properties_amenities(property_id,amenity_id) VALUES(?,?)");
        foreach($_POST['amenities'] as $am){
            $st->execute([$property_id,$am]);
        }
    }

    /* IMAGE UPLOAD */
    if(!empty($_FILES['property_images']['name'][0])){
        $folder = __DIR__."/../assets/pg_images/property_$property_id/";
        if(!is_dir($folder)) mkdir($folder,0777,true);

        foreach($_FILES['property_images']['name'] as $i=>$n){
            if($_FILES['property_images']['error'][$i]!=0) continue;

            $ext=strtolower(pathinfo($n,PATHINFO_EXTENSION));
            $new=uniqid("pg_",true).".".$ext;

            if(move_uploaded_file($_FILES['property_images']['tmp_name'][$i],$folder.$new)){
                $path="assets/pg_images/property_$property_id/$new";
                $pdo->prepare("INSERT INTO property_images(property_id,image_path) VALUES(?,?)")
                ->execute([$property_id,$path]);
            }
        }
    }

    header("Location: properties.php");
    exit;
}

include 'includes/header.php';
?>

<form method="post" enctype="multipart/form-data" class="card p-4">

<h4><?= $id?'Edit Property':'Add Property'; ?></h4>

<select name="city_id" class="form-control mb-2" required>
<option value="">-- Select City --</option>
<?php
$cities=$pdo->query("SELECT * FROM cities");
while($ct=$cities->fetch(PDO::FETCH_ASSOC)){
$sel = (($prop['city_id']??0)==$ct['id'])?'selected':'';
echo "<option value='{$ct['id']}' $sel>{$ct['name']}</option>";
}
?>
</select>

<input name="name" class="form-control mb-2" placeholder="Property Name" value="<?= $prop['name']??'' ?>">
<textarea name="address" class="form-control mb-2" placeholder="Address"><?= $prop['address']??'' ?></textarea>
<textarea name="description" class="form-control mb-2" placeholder="Description"><?= $prop['description']??'' ?></textarea>

<select name="gender" class="form-control mb-2">
<option value="male" <?=($prop['gender']??'')=='male'?'selected':''?>>Male</option>
<option value="female" <?=($prop['gender']??'')=='female'?'selected':''?>>Female</option>
<option value="other" <?=($prop['gender']??'')=='other'?'selected':''?>>Other</option>
</select>

<input type="number" name="rent" class="form-control mb-2" placeholder="Rent" value="<?= $prop['rent']??'' ?>">
<input type="number" name="rating_clean" class="form-control mb-2" placeholder="Clean Rating" value="<?= $prop['rating_clean']??'' ?>">
<input type="number" name="rating_food" class="form-control mb-2" placeholder="Food Rating" value="<?= $prop['rating_food']??'' ?>">
<input type="number" name="rating_safety" class="form-control mb-2" placeholder="Safety Rating" value="<?= $prop['rating_safety']??'' ?>">

<label>Upload Images</label>
<input type="file" name="property_images[]" multiple class="form-control mb-2">

<h5>Existing Images</h5>
<div style="display:flex;flex-wrap:wrap;">
<?php
if($id){
$imgs=$pdo->prepare("SELECT * FROM property_images WHERE property_id=?");
$imgs->execute([$id]);
foreach($imgs as $img){
?>
<div style="margin:10px;text-align:center;">
<img src="../<?= $img['image_path'] ?>" width="120"><br>
<a href="?id=<?= $id ?>&delete_img=<?= $img['id'] ?>" 
class="btn btn-sm btn-danger"
onclick="return confirm('Delete this image?')">Delete</a>
</div>
<?php }} ?>
</div>

<h5>Amenities</h5>
<?php foreach($allAmenities as $a){ ?>
<label>
<input type="checkbox" name="amenities[]" value="<?=$a['id']?>" <?= in_array($a['id'],$selectedAmenities)?'checked':'' ?>>
<?=$a['name']?>
</label><br>
<?php } ?>

<br>
<button class="btn btn-primary">Save</button>
</form>

<?php include 'includes/footer.php'; ?>
