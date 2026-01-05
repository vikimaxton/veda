import { Head, Link, router } from '@inertiajs/react';

interface Theme {
    id: string;
    name: string;
    slug: string;
    version: string;
    author: string;
    description: string;
    is_active: boolean;
    screenshot_url?: string;
}

interface ThemesProps {
    auth: {
        user: {
            name: string;
            email: string;
        };
    };
    themes: Theme[];
}

export default function Themes({ auth, themes }: ThemesProps) {
    const handleActivate = async (slug: string) => {
        try {
            const csrfToken = document.head.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            const response = await fetch(`/api/cms/themes/${slug}/activate`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || '',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
            });

            if (response.ok) {
                // Reload the page to show updated data
                router.visit(window.location.pathname, {
                    preserveState: false,
                    preserveScroll: true,
                });
            } else {
                const data = await response.json();
                alert(`Failed to activate theme: ${data.message || 'Unknown error'}`);
            }
        } catch (error) {
            console.error('Error activating theme:', error);
            alert('An error occurred while activating the theme');
        }
    };


    const activeTheme = themes.find(t => t.is_active);

    return (
        <>
            <Head title="Themes - CMS Admin" />

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
                            <Link href="/admin/themes" className="flex items-center px-4 py-2 text-sm font-medium text-gray-900 dark:text-white bg-gray-100 dark:bg-gray-700 rounded-lg">
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
                            <Link href="/admin/settings" className="flex items-center px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                                <svg className="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                Settings
                            </Link>
                        </nav>
                    </aside>

                    <main className="flex-1 p-8">
                        <div className="max-w-7xl mx-auto">
                            <div className="mb-8">
                                <h2 className="text-3xl font-bold text-gray-900 dark:text-white">Themes</h2>
                                <p className="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                    Customize your site's appearance with themes
                                </p>
                            </div>

                            {activeTheme && (
                                <div className="mb-8 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                                    <div className="flex items-center">
                                        <svg className="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span className="text-sm font-medium text-blue-800 dark:text-blue-300">
                                            Active Theme: <strong>{activeTheme.name}</strong> v{activeTheme.version}
                                        </span>
                                    </div>
                                </div>
                            )}

                            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                {themes.map((theme) => (
                                    <div
                                        key={theme.id}
                                        className={`bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden ${theme.is_active ? 'ring-2 ring-blue-500' : ''
                                            }`}
                                    >
                                        <div className="aspect-video bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                                            {theme.screenshot_url ? (
                                                <img src={theme.screenshot_url} alt={theme.name} className="w-full h-full object-cover" />
                                            ) : (
                                                <svg className="w-16 h-16 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                                                </svg>
                                            )}
                                        </div>

                                        <div className="p-6">
                                            <div className="flex items-start justify-between mb-2">
                                                <h3 className="text-lg font-semibold text-gray-900 dark:text-white">{theme.name}</h3>
                                                {theme.is_active && (
                                                    <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                        Active
                                                    </span>
                                                )}
                                            </div>

                                            <p className="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                                {theme.description}
                                            </p>

                                            <div className="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 mb-4">
                                                <span>v{theme.version}</span>
                                                <span>by {theme.author}</span>
                                            </div>

                                            {!theme.is_active && (
                                                <button
                                                    onClick={() => handleActivate(theme.slug)}
                                                    className="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors"
                                                >
                                                    Activate
                                                </button>
                                            )}
                                        </div>
                                    </div>
                                ))}
                            </div>

                            {themes.length === 0 && (
                                <div className="text-center py-12 bg-white dark:bg-gray-800 rounded-lg shadow">
                                    <svg className="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                                    </svg>
                                    <p className="text-gray-600 dark:text-gray-400">No themes found. Add themes to the /themes directory.</p>
                                </div>
                            )}
                        </div>
                    </main>
                </div>
            </div>
        </>
    );
}
