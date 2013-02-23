<?php
$noauth_ok = true;
require('header.php');
?>
<div class="content">
    <div class="info">
        <h2 class="info_text">Making Fandom Social</h2>
        <p class="info_text">Out*give"\, v.t. To Surpass in giving</p>
        <h3 class="info_text">Why <span style="color:#385997">Outgive?</span></h3>
        <p></p>
        <p class="info_text">We want to make donating to your College or University simple, easy, and transparent. Our goal is to reduce the unnecessary emails, phonecalls, and mailers, and make the donation process enjoyable.</p>
        <!--
        <p class="info_text">Follow your friends and peers in the largest online race for school pride.</p>
        -->
        
    </div>
    
    <div class="log_on" id="fb-signin">
        <?php
            //if(!check_auth()) {
        ?>

    <!--
        <iframe src="https://docs.google.com/a/u.northwestern.edu/spreadsheet/embeddedform?formkey=dFNReXVzdjBaU2c3QTdSTENMMXVnX2c6MQ&bc=E8D889" width="375" height="300" frameborder="0" marginheight="0" marginwidth="0">Loading...</iframe>
        -->

        <ul class="instruct">
            <li>1. Log in with Facebook.</li>
            <li>2. Find the schools you care about.</li>
            <li>3. Donate in a safe, secure way to your school.</li>
            <li>4. Review your donation history, receive tax documents, and compare your donations to your friends and classmates.</li>
        </ul>

            <fb:login-button max-rows="1" perms="email,user_education_history,offline_access,publish_stream">Login with Facebook</fb:login-button>
            <?php
            //}
            //    else {
			//	$user = get_user();
            ?>
           
            <h2>Welcome <?php// echo htmlspecialchars($user['name']); ?></h2>
			<?php //echo "<!--"; print_r($user); echo "-->"; ?>
			<p> Visit your Outboard to manage your stats </p>
			<span class="action-link"><a href="/outboard.php">Visit your Outboard</a></span>
			
		<?php
		//		}
		?>
    </div>
</div>
<?php
require('footer.php');
?>