<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Session Expired</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @vite(['resources/css/app.css'])
</head>

<body class="min-h-screen flex items-center justify-center
             bg-[#021420] text-gray-200 px-4">

    <div class="w-full max-w-md bg-[#0b2436]
                border border-[#1f425a]
                rounded-xl shadow-lg p-8 text-center">

        {{-- ICON --}}
        <div class="mx-auto mb-6 w-14 h-14 rounded-full
                    bg-red-600/20 flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg"
                 class="h-7 w-7 text-red-400"
                 fill="none" viewBox="0 0 24 24"
                 stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M12 9v2m0 4h.01M5.07 19h13.86
                         c1.54 0 2.5-1.67 1.73-3L13.73 4
                         c-.77-1.33-2.69-1.33-3.46 0L3.34 16
                         c-.77 1.33.19 3 1.73 3z"/>
            </svg>
        </div>

        {{-- TITLE --}}
        <h1 class="text-xl font-semibold text-white mb-2">
            Session Expired
        </h1>

        {{-- MESSAGE --}}
        <p class="text-sm text-gray-400 mb-6 leading-relaxed">
            Your session has expired or is invalid.
            Please return to the main application and log in again
            to continue.
        </p>

        {{-- ACTION --}}
        <a href="{{ config('services.partakers.login_url') ?? '#' }}"
           class="inline-flex items-center justify-center
                  bg-blue-600 hover:bg-blue-700
                  text-white font-medium
                  px-6 py-3 rounded-lg transition">
            Go Back to Login
        </a>

        {{-- FOOTER --}}
        <p class="text-xs text-gray-500 mt-6">
            © {{ date('Y') }} Notice Management System
        </p>
    </div>

</body>
</html>
