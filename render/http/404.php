<?php
$this->layout('layouts/page', [
    'title' => 'Page not found',
]);
?>
<div class="mx-auto flex w-full max-w-7xl flex-grow flex-col justify-center px-6 lg:px-8">
    <div class="py-16">
        <div class="text-center space-y-5">
            <p class="text-4xl font-semibold text-blue-600">404</p>
            <h1 class="mt-2 text-4xl font-bold tracking-tight text-slate-700 sm:text-4xl">Page not found.</h1>
            <p class="mt-2 text-base text-slate-500">Sorry, we couldn’t find the page you’re looking for.</p>
            <div class="mt-6">
                <a href="/" class="text-base font-medium text-blue-600 hover:text-blue-500">
                    Go back home
                    <span aria-hidden="true"> &rarr;</span>
                </a>
            </div>
        </div>
    </div>
</div>

