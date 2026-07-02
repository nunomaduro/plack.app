import { Link } from '@inertiajs/react';
import type { ComponentProps } from 'react';
import { cn } from '@/lib/utils';

type Props = ComponentProps<typeof Link>;

export default function TextLink({
    className = '',
    children,
    ...props
}: Props) {
    return (
        <Link
            className={cn(
                'text-dim underline decoration-line underline-offset-4 transition-colors duration-200 ease-out hover:text-amber hover:decoration-amber',
                className,
            )}
            {...props}
        >
            {children}
        </Link>
    );
}
