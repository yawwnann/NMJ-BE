@props(['disabled' => false])

<input @disabled($disabled)
    {{ $attributes->merge(['class' => 'border-gray-300 focus:border-blue-800 focus:ring-blue-800 rounded-lg shadow-sm transition duration-200 ease-in-out']) }}>