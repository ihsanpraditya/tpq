export type UserRole = 'admin' | 'pengajar' | 'wali';

export interface AuthUser {
	id: number;
	name: string;
	username: string;
	email: string | null;
	role: UserRole;
	is_active: 0 | 1;
	last_login_at: string | null;
}

export interface LoginResponse {
	token: string;
	user: AuthUser;
}

export interface ApiErrorResponse {
	message: string;
}
