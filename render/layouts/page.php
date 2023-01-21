<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap">
        <title><?php echo $this->escape($title ?? 'Page') ?></title>
        <style>
            * { font-family: 'Inter', sans-serif; }
        </style>
    </head>
    <body class="bg-white flex flex-col items-center justify-center min-h-screen">
        <main class="w-full max-w-2xl flex flex-col p-10 gap-y-10">
            <?php echo $this->section('content'); ?>
        </main>

        <script src="https://cdn.tailwindcss.com"></script>
        <script src="<?php echo $this->asset('/assets/tailwind.js'); ?>"></script>
    </body>
</html>
