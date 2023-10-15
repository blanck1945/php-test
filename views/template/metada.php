    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?php foreach ($page_data['metadata']['css'] as $css_file) : ?>
        <style>
            <?php include(__DIR__ . '/../css/' . $css_file) ?>
        </style>
    <?php endforeach ?>

    <meta property="og:title" content="Phantom App" />
    <meta property="og:description" content="Light PHP framework por productivity" />
    <meta property="og:image" content="https://omniglot.com/images/langsamples/udhr_japanese-vert.gif" />
    <title>
        Phantom App
    </title>