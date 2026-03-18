export interface Room {
    id: number;
    code: string;
    icon: string;
    theme: string;
    type: string;
    max_users: number;
    game_mode: string;
    status: string;
    user_id: number;
    users_count: number;
    created_at: string;
    updated_at: string;
}