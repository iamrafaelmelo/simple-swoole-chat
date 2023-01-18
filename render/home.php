<?php
$this->layout('layouts/app', [
    'title' => Chat\App::container()->get('settings')['app']['name'],
]);
?>

<section class="p-8 flex flex-col gap-y-2 border border-slate-300 rounded-lg">
    <messages class="mb-6 h-96 max-h-full overflow-y-auto empty:mb-0 empty:h-0"></messages>
    <form class="flex flex-col w-full space-y-4">
        <label>
            <input
                class="px-3 py-2 w-full border border-slate-300 rounded placeholder-slate-400 placeholder-opacity-80 focus:outline-none focus:ring-4 focus:ring-blue-200 focus:border-blue-600"
                type="text"
                placeholder="Type here... (Press 'Enter' to send message)"
                autofocus
            >
        </label>
    </form>
    <typing class="text-sm text-slate-500"></typing>
</section>
