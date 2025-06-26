<!-- resources/views/components/app-layout.blade.php -->
<div>
    <!-- Page Header -->
    @isset($header)
        <header class="bg-white dark:bg-zinc-800 shadow p-4 text-lg font-semibold text-gray-800 dark:text-white">
            {{ $header }}
        </header>
    @endisset

    <!-- Page Content -->
    <main class="p-6">
        {{ $slot }}
    </main>
</div>