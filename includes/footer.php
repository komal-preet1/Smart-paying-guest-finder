<?php
  // Property list page path (relative, works on localhost + live)
  $property_list_path = "property_list.php";
?>

<!-- Footer Section -->
<div class="footer">
    <div class="page-container footer-container">
        <div class="footer-cities">
            <div class="footer-city">
                <a href="<?php echo $property_list_path.'?city=Delhi'; ?>">PG in Delhi</a>
            </div>
            <div class="footer-city">
                <a href="<?php echo $property_list_path.'?city=Mumbai'; ?>">PG in Mumbai</a>
            </div>
            <div class="footer-city">
                <a href="<?php echo $property_list_path.'?city=Bengaluru'; ?>">PG in Bangalore</a>
            </div>
            <div class="footer-city">
                <a href="<?php echo $property_list_path.'?city=Hyderabad'; ?>">PG in Hyderabad</a>
            </div>
        </div>

        <div class="footer-extra">
            <span>SmartPGFinder • Helping students find safe & affordable PGs</span>
        </div>

        <div class="footer-copyright">
            © 2025 SmartPGFinder | All rights reserved
        </div>
    </div>
</div>
