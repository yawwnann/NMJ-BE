<button
    {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-6 py-3 bg-blue-800 border border-transparent rounded-lg font-medium text-sm text-white uppercase tracking-wide hover:bg-blue-900 focus:bg-blue-900 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-blue-800 focus:ring-offset-2 transition ease-in-out duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5']) }}>
    {{ $slot }}
</button>