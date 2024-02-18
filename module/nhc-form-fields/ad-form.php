<div class="container">
<style>
    .aq_hide{
        display: none;
    }
    .tabs {
        list-style: none;
        padding: 0;
        margin: 0;
        background-color: #050333;
        display: flex;
        justify-content: center;
        border-radius: 8px 8px 0 0;
    }

    .tabs li {
        display: inline-block;
    }

    .tabs li a {
        display: block;
        color: #fff;
        text-decoration: none;
        padding: 10px 20px;
        background-color: #050333;
        transition: background-color 0.3s;
    }

    .tabs li a:hover,
    .tabs li a.active {
        background-color: #28a745;
    }

    .tab-content {
        display: none;
        padding: 20px;
        background-color: #fff;
        border: 1px solid #ccc;
    }

    .tab-content h4, .tab-content h3 {
        color: #03A9F4;
        font-size: 1rem;
        border: 1px solid;
        padding: 1rem;
        margin-top: 1rem;
    }

    input[type="text"],
    input[type="email"],
    input[type="submit"] {
        padding: 10px;
        margin-bottom: 10px;
        width: 100%;
        box-sizing: border-box;
    }

    input[type="submit"] {
        background-color: #333;
        color: #fff;
        border: none;
        cursor: pointer;
    }

    input[type="submit"]:hover {
        background-color: #555;
    }

    .tab-content {
        display: none;
    }
</style>

    <script>
        jQuery(document).ready(function($) {
            $('.tab-link').on('click', function(e) {
                e.preventDefault();
                var target = $(this).data('target');
                $('.tab-link').removeClass('active');
                $(this).addClass('active');
                $('.tab-content').hide();
                $(target).show();
            });
        });
    </script>

    <ul class="tabs">
        <li><a href="#" class="tab-link" data-target="#form1">اضافة اعلان الي المنصة</a></li>
        <li><a href="#" class="tab-link active" data-target="#form2">إنشاء ترخيص إعلان</a></li>
    </ul>

    <?php 
    include ('add-ad.php');  
    include ('create-ad.php'); 
    ?>
</div>