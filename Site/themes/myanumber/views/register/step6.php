<div class="register">
    <h1>Almost Done!</h1>
    <div class="register-top"></div>
    <div class="register-content">
        <h3>An email has been sent to:</h3>
        <?php
        if ($email) {
            ?>
            <div class="register-email"><?php echo $email; ?></div>
            <?php
        }
        else {
            ?>
            <p>There was a problem sending the activation email. Please contact support.</p>
            <?php
        }
        ?>
        <h3>Follow the link to confirm your email and to choose your myaNUMBER.</h3>
        <p style="font-size: 19px">Have not received your confirmation email? Check your SPAM or click <a style="color: #52c5d0;" href="javascript:document.location.reload(true)">here</a> to resend.</p>
    </div>
    <div class="register-bottom"></div>
</div>
