<?php
/*
Template Name: API Tester
*/

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['url'])) {
    $url = filter_var($_POST['url'], FILTER_SANITIZE_URL);
    $method = sanitize_text_field($_POST['method']);
    $headers = json_decode(stripslashes($_POST['headers']), true);
    $body = stripslashes($_POST['body']); // Decode JSON strings

    $options = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER => false,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_POSTFIELDS => $body,
    ];

    if (!empty($headers)) {
        $formattedHeaders = [];
        foreach ($headers as $key => $value) {
            $formattedHeaders[] = "$key: $value";
        }
        $options[CURLOPT_HTTPHEADER] = $formattedHeaders;
    }

    $ch = curl_init($url);
    curl_setopt_array($ch, $options);
    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        $response = "cURL Error: $error";
    }

    // Keep the response in $_POST for display
    $_POST['response'] = $response;
}
// var_dump(get_post_meta(32392, 'fave_property_images', true));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Tester</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.23.0/themes/prism-okaidia.min.css" rel="stylesheet">
</head>
<body>
<div class="container">
    <h1 class="mt-5">API Tester</h1>
    <form id="apiForm" method="POST" action="" class="mt-3">
        <div class="form-group">
            <label for="url">Request URL</label>
            <input type="url" class="form-control" id="url" name="url" required value="<?= esc_attr($_POST['url'] ?? $_GET['url'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label for="method">Request Method</label>
            <select class="form-control" id="method" name="method">
                <option value="GET" <?= ($_POST['method'] ?? $_GET['method'] ?? '') === 'GET' ? 'selected' : '' ?>>GET</option>
                <option value="POST" <?= ($_POST['method'] ?? $_GET['method'] ?? '') === 'POST' ? 'selected' : '' ?>>POST</option>
                <option value="PUT" <?= ($_POST['method'] ?? $_GET['method'] ?? '') === 'PUT' ? 'selected' : '' ?>>PUT</option>
                <option value="DELETE" <?= ($_POST['method'] ?? $_GET['method'] ?? '') === 'DELETE' ? 'selected' : '' ?>>DELETE</option>
            </select>
        </div>
        <div class="form-group">
            <label for="headers">Headers (JSON)</label>
            <textarea class="form-control" id="headers" name="headers" rows="3"><?= esc_textarea(stripslashes($_POST['headers'] ?? stripslashes($_GET['headers']) ?? '')) ?></textarea>
        </div>
        <div class="form-group">
            <label for="body">Request Body (JSON)</label>
            <textarea class="form-control" id="body" name="body" rows="3"><?= esc_textarea(stripslashes($_POST['body'] ?? stripslashes($_GET['body']) ?? '')) ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Send Request</button>
    </form>
    <?php if (isset($_POST['response'])): ?>
        <div class="mt-5">
            <h3>Response:</h3>
            <pre class="language-json"><code><?= esc_html($_POST['response']) ?></code></pre>
        </div>
    <?php endif; ?>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.23.0/prism.min.js"></script>
</body>
</html>