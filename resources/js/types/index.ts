export type * from './auth';
export type * from './navigation';
export type * from './ui';

export interface Paginated<T> {
    data: T[]

    current_page: number
    from: number | null
    last_page: number
    per_page: number
    to: number | null
    total: number

    first_page_url: string
    last_page_url: string
    next_page_url: string | null
    prev_page_url: string | null
    path: string

    links: {
        url: string | null
        label: string
        active: boolean
    }[]
}
