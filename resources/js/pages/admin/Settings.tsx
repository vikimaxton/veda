import { Head, Link, router, useForm } from '@inertiajs/react';
import { useState } from 'react';

interface SettingsProps {
    auth: {
        user: {
            name: string;
            email: string;
        };
    };
    settings: {
        site_title?: string;
        site_tagline?: string;
        site_logo?: string;
        logo_size?: string;
        timezone?: string;
        default_meta_title?: string;
        default_meta_description?: string;
        cache_ttl?: string;
        debug_mode?: string;
    };
}

export default function Settings({ auth, settings }: SettingsProps) {
    const [activeTab, setActiveTab] = useState<'general' | 'seo' | 'advanced'>('general');

    const { data, setData, processing, errors } = useForm({
        site_title: settings.site_title || '',
        site_tagline: settings.site_tagline || '',
        site_logo: settings.site_logo || '',
        logo_size: settings.logo_size || '100',
        timezone: settings.timezone || 'UTC',
        default_meta_title: settings.default_meta_title || '',
        default_meta_description: settings.default_meta_description || '',
        cache_ttl: settings.cache_ttl || '3600',
        debug_mode: settings.debug_mode || 'false',
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        router.put('/admin/settings', data as any, {
            onSuccess: () => {
                alert('Settings saved successfully!');
            },
            onError: (errors) => {
                console.error('Settings save error:', errors);
                alert('Failed to save settings');
            },
        });
    };

    const tabs = [
        { id: 'general', name: 'General', icon: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z' },
        { id: 'seo', name: 'SEO', icon: 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z' },
        { id: 'advanced', name: 'Advanced', icon: 'M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4' },
    ];

    return (
        <>
            <Head title="Settings - CMS Admin" />

            <div className="min-h-screen bg-gray-50 dark:bg-gray-900">
                <header className="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <div className="px-4 sm:px-6 lg:px-8">
                        <div className="flex h-16 items-center justify-between">
                            <div className="flex items-center">
                                <Link href="/admin" className="text-2xl font-bold text-gray-900 dark:text-white hover:text-gray-700">
                                    CMS Admin
                                </Link>
                            </div>

                            <div className="flex items-center gap-4">
                                <span className="text-sm text-gray-600 dark:text-gray-300">{auth.user.name}</span>
                                <Link href="/logout" method="post" as="button" className="text-sm text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white">
                                    Logout
                                </Link>
                            </div>
                        </div>
                    </div>
                </header>

                <div className="flex">
                    <aside className="w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 min-h-[calc(100vh-4rem)]">
                        <nav className="p-4 space-y-1">
                            <Link href="/admin" className="flex items-center px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                                <svg className="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                                Dashboard
                            </Link>
                            <Link href="/admin/pages" className="flex items-center px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                                <svg className="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                Pages
                            </Link>
                            <Link href="/admin/themes" className="flex items-center px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                                <svg className="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" /></svg>
                                Themes
                            </Link>
                            <Link href="/admin/plugins" className="flex items-center px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                                <svg className="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z" /></svg>
                                Plugins
                            </Link>
                            <Link href="/admin/users" className="flex items-center px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                                <svg className="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                                Users & Roles
                            </Link>
                            <Link href="/admin/settings" className="flex items-center px-4 py-2 text-sm font-medium text-gray-900 dark:text-white bg-gray-100 dark:bg-gray-700 rounded-lg">
                                <svg className="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                Settings
                            </Link>
                        </nav>
                    </aside>

                    <main className="flex-1 p-8">
                        <div className="max-w-4xl mx-auto">
                            <div className="mb-8">
                                <h2 className="text-3xl font-bold text-gray-900 dark:text-white">Settings</h2>
                                <p className="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                    Configure your CMS settings
                                </p>
                            </div>

                            {/* Tabs */}
                            <div className="border-b border-gray-200 dark:border-gray-700 mb-6">
                                <nav className="-mb-px flex space-x-8">
                                    {tabs.map((tab) => (
                                        <button
                                            key={tab.id}
                                            onClick={() => setActiveTab(tab.id as any)}
                                            className={`flex items - center py - 4 px - 1 border - b - 2 font - medium text - sm transition - colors ${activeTab === tab.id
                                                ? 'border-blue-500 text-blue-600 dark:text-blue-400'
                                                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'
                                                } `}
                                        >
                                            <svg className="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d={tab.icon} />
                                            </svg>
                                            {tab.name}
                                        </button>
                                    ))}
                                </nav>
                            </div>

                            {/* Settings Form */}
                            <form onSubmit={handleSubmit}>
                                <div className="bg-white dark:bg-gray-800 rounded-lg shadow p-6 space-y-6">
                                    {/* General Tab */}
                                    {activeTab === 'general' && (
                                        <>
                                            {/* Logo Upload Section */}
                                            <div className="pb-6 border-b border-gray-200 dark:border-gray-700">
                                                <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">Site Logo</h3>

                                                <div className="space-y-4">
                                                    <div>
                                                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                            Upload Logo (SVG or PNG)
                                                        </label>
                                                        <input
                                                            type="file"
                                                            accept=".svg,.png,image/svg+xml,image/png"
                                                            onChange={(e) => {
                                                                const file = e.target.files?.[0];
                                                                if (file) {
                                                                    const reader = new FileReader();
                                                                    reader.onloadend = () => {
                                                                        setData('site_logo', reader.result as string);
                                                                    };
                                                                    reader.readAsDataURL(file);
                                                                }
                                                            }}
                                                            className="block w-full text-sm text-gray-900 dark:text-white border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-700 focus:outline-none"
                                                        />
                                                        <p className="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                                            Recommended: SVG for best quality. Max file size: 2MB
                                                        </p>
                                                    </div>

                                                    {data.site_logo && (
                                                        <>
                                                            <div>
                                                                <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                                    Logo Size: {data.logo_size || 100}px
                                                                </label>
                                                                <div className="flex items-center gap-4">
                                                                    <button
                                                                        type="button"
                                                                        onClick={() => setData('logo_size', Math.max(50, (parseInt(data.logo_size) || 100) - 10).toString())}
                                                                        className="px-3 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600"
                                                                    >
                                                                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M20 12H4" />
                                                                        </svg>
                                                                    </button>
                                                                    <input
                                                                        type="range"
                                                                        min="50"
                                                                        max="300"
                                                                        step="10"
                                                                        value={data.logo_size || 100}
                                                                        onChange={(e) => setData('logo_size', e.target.value)}
                                                                        className="flex-1 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700"
                                                                    />
                                                                    <button
                                                                        type="button"
                                                                        onClick={() => setData('logo_size', Math.min(300, (parseInt(data.logo_size) || 100) + 10).toString())}
                                                                        className="px-3 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600"
                                                                    >
                                                                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 4v16m8-8H4" />
                                                                        </svg>
                                                                    </button>
                                                                </div>
                                                            </div>

                                                            <div className="bg-gray-100 dark:bg-gray-700 rounded-lg p-6 flex items-center justify-center">
                                                                <img
                                                                    src={data.site_logo}
                                                                    alt="Site Logo Preview"
                                                                    style={{ width: `${data.logo_size || 100} px`, height: 'auto' }}
                                                                    className="max-w-full"
                                                                />
                                                            </div>

                                                            <button
                                                                type="button"
                                                                onClick={() => {
                                                                    setData('site_logo', '');
                                                                    setData('logo_size', '100');
                                                                }}
                                                                className="text-sm text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"
                                                            >
                                                                Remove Logo
                                                            </button>
                                                        </>
                                                    )}
                                                </div>
                                            </div>

                                            {/* Site Information */}
                                            <div className="pt-6">
                                                <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">Site Information</h3>

                                                <div className="space-y-4">
                                                    <div>
                                                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                            Site Title
                                                        </label>
                                                        <input
                                                            type="text"
                                                            value={data.site_title}
                                                            onChange={(e) => setData('site_title', e.target.value)}
                                                            className="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                            placeholder="My Awesome Site"
                                                        />
                                                    </div>

                                                    <div>
                                                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                            Site Tagline
                                                        </label>
                                                        <input
                                                            type="text"
                                                            value={data.site_tagline}
                                                            onChange={(e) => setData('site_tagline', e.target.value)}
                                                            className="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                            placeholder="Just another WordPress-like CMS"
                                                        />
                                                    </div>

                                                    <div>
                                                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                            Timezone
                                                        </label>
                                                        <select
                                                            value={data.timezone}
                                                            onChange={(e) => setData('timezone', e.target.value)}
                                                            className="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                        >
                                                            <option value="UTC">UTC</option>
                                                            <option value="America/New_York">America/New_York</option>
                                                            <option value="America/Chicago">America/Chicago</option>
                                                            <option value="America/Los_Angeles">America/Los_Angeles</option>
                                                            <option value="Europe/London">Europe/London</option>
                                                            <option value="Asia/Kolkata">Asia/Kolkata</option>
                                                            <option value="Asia/Tokyo">Asia/Tokyo</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </>
                                    )}

                                    {/* SEO Tab */}
                                    {activeTab === 'seo' && (
                                        <>
                                            <div>
                                                <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                    Default Meta Title
                                                </label>
                                                <input
                                                    type="text"
                                                    value={data.default_meta_title}
                                                    onChange={(e) => setData('default_meta_title', e.target.value)}
                                                    className="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                    placeholder="Default title for pages without custom meta title"
                                                />
                                            </div>

                                            <div>
                                                <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                    Default Meta Description
                                                </label>
                                                <textarea
                                                    value={data.default_meta_description}
                                                    onChange={(e) => setData('default_meta_description', e.target.value)}
                                                    rows={4}
                                                    className="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                    placeholder="Default description for pages without custom meta description"
                                                />
                                            </div>
                                        </>
                                    )}

                                    {/* Advanced Tab */}
                                    {activeTab === 'advanced' && (
                                        <>
                                            <div>
                                                <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                    Cache TTL (seconds)
                                                </label>
                                                <input
                                                    type="number"
                                                    value={data.cache_ttl}
                                                    onChange={(e) => setData('cache_ttl', e.target.value)}
                                                    className="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                    placeholder="3600"
                                                />
                                                <p className="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                                    Time to live for cached content in seconds (default: 3600 = 1 hour)
                                                </p>
                                            </div>

                                            <div>
                                                <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                    Debug Mode
                                                </label>
                                                <select
                                                    value={data.debug_mode}
                                                    onChange={(e) => setData('debug_mode', e.target.value)}
                                                    className="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                >
                                                    <option value="false">Disabled</option>
                                                    <option value="true">Enabled</option>
                                                </select>
                                                <p className="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                                    Enable debug mode to see detailed error messages (not recommended for production)
                                                </p>
                                            </div>
                                        </>
                                    )}
                                </div>

                                {/* Save Button */}
                                <div className="mt-6 flex justify-end">
                                    <button
                                        type="submit"
                                        disabled={processing}
                                        className="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors disabled:opacity-50"
                                    >
                                        {processing ? 'Saving...' : 'Save Settings'}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </main>
                </div>
            </div>
        </>
    );
}
