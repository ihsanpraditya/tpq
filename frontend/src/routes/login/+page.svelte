<script lang="ts">
	import { goto } from '$app/navigation';
	import { apiFetch, ApiError } from '$lib/api/client';
	import { auth } from '$lib/stores/auth.svelte';
	import type { LoginResponse } from '$lib/types/auth';

	let identifier = $state('');
	let password = $state('');
	let rememberMe = $state(true);
	let showPassword = $state(false);
	let isSubmitting = $state(false);
	let errorMessage = $state<string | null>(null);

	async function handleSubmit(event: SubmitEvent): Promise<void> {
		event.preventDefault();
		errorMessage = null;

		if (!identifier.trim() || !password) {
			errorMessage = 'Username/email dan kata sandi wajib diisi.';
			return;
		}

		isSubmitting = true;

		try {
			const result = await apiFetch<LoginResponse>('/api/auth/login', {
				method: 'POST',
				body: { identifier: identifier.trim(), password }
			});

			auth.setSession(result.token, result.user, rememberMe);
			await goto('/');
		} catch (error) {
			errorMessage =
				error instanceof ApiError ? error.message : 'Tidak bisa terhubung ke server. Coba lagi.';
		} finally {
			isSubmitting = false;
		}
	}
</script>

<svelte:head>
	<title>Masuk — TPQ</title>
</svelte:head>

<div class="grid min-h-svh lg:grid-cols-2">
	<!-- Panel identitas -->
	<div
		class="relative isolate flex flex-col justify-between overflow-hidden bg-emerald-deep px-8 py-10 text-cream sm:px-12 lg:px-16 lg:py-14"
	>
		<svg
			class="pointer-events-none absolute inset-0 h-full w-full opacity-[0.16]"
			aria-hidden="true"
		>
			<defs>
				<pattern id="rub-el-hizb" width="88" height="88" patternUnits="userSpaceOnUse">
					<g fill="none" stroke="var(--color-gold-soft)" stroke-width="1">
						<rect x="20" y="20" width="48" height="48" transform="rotate(0 44 44)" />
						<rect x="20" y="20" width="48" height="48" transform="rotate(45 44 44)" />
					</g>
				</pattern>
			</defs>
			<rect width="100%" height="100%" fill="url(#rub-el-hizb)" />
		</svg>

		<span class="relative font-display text-lg text-gold-soft">TPQ</span>

		<div class="relative max-w-sm">
			<p class="font-display text-xl italic text-gold-soft">بسم الله الرحمن الرحيم</p>
			<h1 class="mt-4 font-display text-5xl leading-tight sm:text-6xl">
				Sistem Manajemen TPQ
			</h1>
			<p class="mt-5 text-base leading-relaxed text-cream/75">
				Satu tempat untuk mengelola data santri, kelas, jadwal, dan capaian hafalan Taman
				Pendidikan Al-Qur'an Anda.
			</p>
		</div>

		<p class="relative text-sm text-cream/50">&copy; {new Date().getFullYear()} Taman Pendidikan Al-Qur'an</p>
	</div>

	<!-- Panel form -->
	<div class="flex items-center justify-center px-6 py-12 sm:px-12">
		<div class="w-full max-w-sm">
			<p class="font-display text-lg italic text-gold">أهلاً وسهلاً</p>
			<h2 class="mt-2 text-2xl font-semibold text-ink">Masuk ke akun Anda</h2>
			<p class="mt-2 text-sm text-ink-soft">
				Gunakan akun yang diberikan oleh admin TPQ untuk mengakses sistem.
			</p>

			<form class="mt-8 space-y-5" onsubmit={handleSubmit} novalidate>
				{#if errorMessage}
					<div
						role="alert"
						class="rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"
					>
						{errorMessage}
					</div>
				{/if}

				<div>
					<label for="identifier" class="block text-sm font-medium text-ink"
						>Username atau email</label
					>
					<input
						id="identifier"
						name="identifier"
						type="text"
						autocomplete="username"
						bind:value={identifier}
						disabled={isSubmitting}
						class="mt-1.5 block w-full rounded-md border border-ink/15 bg-white px-3.5 py-2.5 text-ink placeholder:text-ink-soft/40 focus:border-emerald focus:outline-none focus:ring-2 focus:ring-emerald/20 disabled:opacity-60"
						placeholder="ustadz.fulan"
					/>
				</div>

				<div>
					<div class="flex items-center justify-between">
						<label for="password" class="block text-sm font-medium text-ink">Kata sandi</label>
						<button
							type="button"
							class="text-sm font-medium text-emerald hover:text-emerald-soft"
							onclick={() => (showPassword = !showPassword)}
						>
							{showPassword ? 'Sembunyikan' : 'Tampilkan'}
						</button>
					</div>
					<input
						id="password"
						name="password"
						type={showPassword ? 'text' : 'password'}
						autocomplete="current-password"
						bind:value={password}
						disabled={isSubmitting}
						class="mt-1.5 block w-full rounded-md border border-ink/15 bg-white px-3.5 py-2.5 text-ink placeholder:text-ink-soft/40 focus:border-emerald focus:outline-none focus:ring-2 focus:ring-emerald/20 disabled:opacity-60"
						placeholder="••••••••"
					/>
				</div>

				<label class="flex items-center gap-2 text-sm text-ink-soft">
					<input
						type="checkbox"
						bind:checked={rememberMe}
						class="h-4 w-4 rounded border-ink/25 text-emerald focus:ring-emerald/30"
					/>
					Ingat saya di perangkat ini
				</label>

				<button
					type="submit"
					disabled={isSubmitting}
					class="w-full rounded-md bg-emerald px-4 py-2.5 font-medium text-cream transition-colors hover:bg-emerald-soft disabled:cursor-not-allowed disabled:opacity-70"
				>
					{isSubmitting ? 'Memproses…' : 'Masuk'}
				</button>
			</form>

			<p class="mt-8 text-sm text-ink-soft">
				Lupa kata sandi atau belum punya akun? Hubungi admin TPQ Anda.
			</p>
		</div>
	</div>
</div>
