<!doctype html>
<html class="theme-<?=$config['theme']?>">
<head>
    <!-- Hide dumps asap -->
    <style>
        pre.sf-dump {
            display: none !important;
        }
    </style>

    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="robots" content="noindex, nofollow">

    <title><?= $title ?></title>

    <?php foreach ($styles as $script): ?>
        <link rel="stylesheet" href="<?=$housekeepingEndpoint?>/styles/<?=$script?>">
    <?php endforeach; ?>

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css">
</head>
<body class="scrollbar-lg">

<script>
    window.data = <?=
        $jsonEncode([
            'report' => $report,
            'config' => $config,
            'solutions' => $solutions,
            'telescopeUrl' => $telescopeUrl,
            'shareEndpoint' => $shareEndpoint,
        ])
    ?>

    window.tabs = <?=$tabs?>;
</script>

<noscript><pre><?=$throwableString?></pre></noscript>

<div id="app"></div>

<script><?= $getAssetContents('ignition.js') ?></script>
<script>
    window.Ignition = window.ignite(window.data);
</script>
<?php foreach ($scripts as $script): ?>
    <script src="<?=$housekeepingEndpoint?>/scripts/<?=$script?>"></script>
<?php endforeach; ?>
<script>
    Ignition.start();
</script>

</body>
</html>
