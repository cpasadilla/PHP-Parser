@props([
    'active' => false,
    'title' => ''
])

<li class="relative leading-8 m-0 pl-6 last:before:bg-white last:before:h-auto last:before:top-4 last:before:bottom-0 dark:last:before:bg-gray-800 before:block before:w-4 before:h-0 before:absolute before:left-0 before:top-4 before:border-t-2 before:border-t-gray-200 before:-mt-0.5 dark:before:border-t-gray-600" x-data="{ open: @json($active) }">
    <button 
        x-on:click="open = !open"
        class="w-full text-left transition-colors hover:text-gray-900 dark:hover:text-gray-100 focus:outline-none flex items-center"
        :class="open ? 'text-gray-900 dark:text-gray-200' : 'text-gray-500 dark:text-gray-400'"
    >
        <span class="flex-1">{{ $title }}</span>
        <svg 
            class="w-3 h-3 ml-2 transition-transform duration-200" 
            :class="{ 'rotate-90': open }"
            fill="currentColor" 
            viewBox="0 0 20 20"
        >
            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 111.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
        </svg>
    </button>
    
    <div
        x-show="open && (isSidebarOpen || isSidebarHovered)"
        x-collapse
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform -translate-y-2"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform -translate-y-2"
    >
        <ul class="relative px-0 pt-1 pb-0 ml-4 before:w-0 before:block before:absolute before:inset-y-0 before:left-2 before:border-l before:border-l-gray-300 dark:before:border-l-gray-500">
            {{ $slot }}
        </ul>
    </div>
</li>
