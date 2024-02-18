<?php
/*
 * Template Name: Delete Account
 */
?>
<head>
    <meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بوابة العقار – منصة العقار السعودية</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>
<!-- Add your HTML and JavaScript code below this line -->
<body class="m-5 p-5 p-sm-1">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-sm-8 col-md-6 text-center">
            <div class="mb-5">
                <?php get_template_part('template-parts/header/partials/logo'); ?>
            </div>
            <button id="delete-account-button" class="btn btn-danger btn-lg">حذف الحساب</button>
            <div id="delete-warning" class="alert alert-danger mt-5">
                <strong>تحذير:</strong> هل أنت متأكد أنك تريد حذف حسابك؟
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script>
    jQuery(document).ready(function($) {
        $('#delete-account-button').on('click', function() {
            var confirmDelete = confirm('هل أنت متأكد أنك تريد حذف حسابك؟');

            if (confirmDelete) {
                var userEmail = prompt('رجاءا أدخل بريدك الإلكتروني / اسم المستخدم:');
                var userPassword = prompt('من فضلك أدخل رقمك السري:');

                if (userEmail === null || userEmail === '' || userPassword === null || userPassword === '') {
                    alert('البريد الإلكتروني وكلمة المرور مطلوبة.');
                    return;
                }

                var confirmDelete = confirm('هل انت متأكد انك تريد حذف حسابك؟');

                if (confirmDelete) {
                    var data = {
                        action: 'aq_delete_account_function',
                        security: '<?php echo wp_create_nonce("delete_account_nonce"); ?>',
                        user_email: userEmail,
                        user_password: userPassword,
                    };

                    $.post('<?php echo admin_url("admin-ajax.php"); ?>', data, function(response) {
                        // Add any additional actions based on the response
                        alert(response);
                    });
                }
            }
        });
    });
</script>  
</body>
