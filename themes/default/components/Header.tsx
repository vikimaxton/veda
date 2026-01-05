import React from 'react';

interface HeaderProps {
    minimal?: boolean;
}

export default function Header({ minimal = false }: HeaderProps) {
    return (
        <header className="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800">
            <div className={minimal ? 'container mx-auto px-4' : 'container mx-auto px-4 py-4'}>
                <div className="flex items-center justify-between">
                    <div className="flex items-center space-x-8">
                        <a href="/" className="text-2xl font-bold text-gray-900 dark:text-white">
                            CMS
                        </a>

                        {!minimal && (
                            <nav className="hidden md:flex space-x-6">
                                <a href="/" className="text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white">
                                    Home
                                </a>
                                <a href="/about" className="text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white">
                                    About
                                </a>
                                <a href="/contact" className="text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white">
                                    Contact
                                </a>
                            </nav>
                        )}
                    </div>

                    <div className="flex items-center space-x-4">
                        <a
                            href="/login"
                            className="text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white"
                        >
                            Login
                        </a>
                    </div>
                </div>
            </div>
        </header>
    );
}
