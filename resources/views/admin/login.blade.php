<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Staff Login — Window Trip</title>
    <link rel="icon" href="/brand/icon.svg" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>
<body class="font-sans antialiased">
    <div class="flex min-h-screen items-center justify-center px-4" style="background:#121026">
        <div class="brand-mesh--dark pointer-events-none absolute inset-0"></div>
        <div class="dot-grid pointer-events-none absolute inset-0 opacity-20"></div>

        <div class="relative w-full max-w-md">
            <div class="mb-8 text-center">
                <img src="/brand/logo-white.svg" alt="Window Trip" class="mx-auto h-10 w-auto">
                <h1 class="mt-6 font-heading text-2xl font-bold text-white">Staff Portal</h1>
                <p class="mt-1 text-sm text-slate-400">Sign in to manage applications & bookings.</p>
            </div>

            <div class="rounded-2xl bg-white p-8 shadow-2xl">
                @if ($errors->any())
                    <div class="mb-5 rounded-xl bg-red-50 px-4 py-3 text-sm font-medium text-red-600">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.login.store') }}" class="space-y-5">
                    @csrf
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700">Email</label>
                        <input name="email" type="email" value="{{ old('email') }}" required autofocus
                            class="w-full rounded-xl border-slate-200 focus:border-brand-purple focus:ring-brand-purple"
                            placeholder="you@windowtrip.test">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700">Password</label>
                        <input name="password" type="password" required
                            class="w-full rounded-xl border-slate-200 focus:border-brand-purple focus:ring-brand-purple"
                            placeholder="••••••••">
                    </div>
                    <label class="flex items-center gap-2 text-sm text-slate-600">
                        <input type="checkbox" name="remember" class="rounded border-slate-300 text-brand-purple focus:ring-brand-purple">
                        Remember me
                    </label>
                    <button type="submit"
                        class="w-full rounded-xl bg-brand-gradient px-6 py-3.5 font-semibold text-white shadow-brand transition hover:opacity-90">
                        Sign in to Admin
                    </button>
                </form>
            </div>

            <p class="mt-6 text-center text-sm text-slate-400">
                Not staff? <a href="{{ url('/') }}" class="font-medium text-white hover:underline">Back to website</a>
            </p>
        </div>
    </div>
</body>
</html>
