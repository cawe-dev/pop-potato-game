export const THEMES = [
    "DESENHOS ANIMADOS",
    "GEOGRAFIA",
    "HISTÓRIA",
    "CONHECIMENTOS GERAIS",
    "SERIES/FILMES",
    "FANTASIA"
] as const;

export type Theme = (typeof THEMES)[number];