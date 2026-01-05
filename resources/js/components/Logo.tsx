import { Link } from '@inertiajs/react';

interface LogoProps {
    logoSettings?: {
        site_logo?: string;
        logo_size?: string;
        site_title?: string;
    };
    maxHeight?: number;
    className?: string;
}

export default function Logo({ logoSettings, maxHeight = 40, className = '' }: LogoProps) {
    if (logoSettings?.site_logo) {
        return (
            <img
                src={logoSettings.site_logo}
                alt={logoSettings.site_title || 'Logo'}
                style={{ height: `${Math.min(parseInt(logoSettings.logo_size || '40'), maxHeight)}px`, width: 'auto' }}
                className={`max-h-10 ${className}`}
            />
        );
    }

    return (
        <span className={`text-2xl font-bold text-gray-900 dark:text-white ${className}`}>
            {logoSettings?.site_title || 'CMS Admin'}
        </span>
    );
}
