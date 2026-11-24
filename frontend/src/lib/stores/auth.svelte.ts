import { browser } from '$app/environment';
import type { AuthUser } from '$lib/types/auth';

const STORAGE_KEY = 'tpq_auth';

interface StoredAuth {
	token: string;
	user: AuthUser;
}

function readStorage(): StoredAuth | null {
	if (!browser) return null;

	try {
		const raw = localStorage.getItem(STORAGE_KEY) ?? sessionStorage.getItem(STORAGE_KEY);
		return raw ? (JSON.parse(raw) as StoredAuth) : null;
	} catch {
		return null;
	}
}

const initial = readStorage();

let token = $state<string | null>(initial?.token ?? null);
let user = $state<AuthUser | null>(initial?.user ?? null);
let remember = $state(true);

function persist(): void {
	if (!browser) return;

	localStorage.removeItem(STORAGE_KEY);
	sessionStorage.removeItem(STORAGE_KEY);

	if (token && user) {
		const store = remember ? localStorage : sessionStorage;
		store.setItem(STORAGE_KEY, JSON.stringify({ token, user }));
	}
}

export const auth = {
	get token() {
		return token;
	},
	get user() {
		return user;
	},
	get isAuthenticated() {
		return token !== null;
	},
	setSession(newToken: string, newUser: AuthUser, rememberMe = true): void {
		token = newToken;
		user = newUser;
		remember = rememberMe;
		persist();
	},
	clear(): void {
		token = null;
		user = null;
		persist();
	}
};
