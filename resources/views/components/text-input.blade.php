@props(['disabled' => false])

<input @disabled($disabled)
    {{ $attributes->merge(['class' => 'rounded-md shadow-sm border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500 invalid:border-red-500 invalid:text-red-600 invalid:focus:border-red-500 invalid:focus:ring-red-500']) }}>
