<x-layouts::app title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">


        <div class="relative h-full flex-1 overflow-auto rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-gray-900">

            <livewire:inventory.product-list />

        </div>
    </div>
</x-layouts::app>
