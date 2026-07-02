import type { HTMLAttributes } from 'react';
import { cn } from '@/lib/utils';

export default function InputError({
    message,
    className = '',
    ...props
}: HTMLAttributes<HTMLParagraphElement> & { message?: string }) {
    return message ? (
        <p
            data-test="input-error"
            {...props}
            className={cn('text-[11px] text-[#d97a4a]', className)}
        >
            {message}
        </p>
    ) : null;
}
