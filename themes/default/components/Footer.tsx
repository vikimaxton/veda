import React from 'react';

interface FooterProps {
    minimal?: boolean;
}

export default function Footer({ minimal = false }: FooterProps) {
    const currentYear = new Date().getFullYear();

    return (
        <footer className="bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800">
            <div className="container mx-auto px-4 py-8">
                {!minimal && (
                    <div className="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                        <div>
                            <h3 className="font-bold text-gray-900 dark:text-white mb-4">About</h3>
                            <p className="text-gray-600 dark:text-gray-400 text-sm">
                                A modern, extensible CMS built with Laravel and React.
                            </p>
                        </div>

                        <div>
                            <h3 className="font-bold text-gray-900 dark:text-white mb-4">Quick Links</h3>
                            <ul className="space-y-2">
                                <li>
                                    <a href="/" className="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white text-sm">
                                        Home
                                    </a>
                                </li>
                                <li>
                                    <a href="/about" className="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white text-sm">
                                        About
                                    </a>
                                </li>
                                <li>
                                    <a href="/contact" className="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white text-sm">
                                        Contact
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <div>
                            <h3 className="font-bold text-gray-900 dark:text-white mb-4">Resources</h3>
                            <ul className="space-y-2">
                                <li>
                                    <a href="/docs" className="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white text-sm">
                                        Documentation
                                    </a>
                                </li>
                                <li>
                                    <a href="/support" className="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white text-sm">
                                        Support
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <div>
                            <h3 className="font-bold text-gray-900 dark:text-white mb-4">Legal</h3>
                            <ul className="space-y-2">
                                <li>
                                    <a href="/privacy" className="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white text-sm">
                                        Privacy Policy
                                    </a>
                                </li>
                                <li>
                                    <a href="/terms" className="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white text-sm">
                                        Terms of Service
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                )}

                <div className={minimal ? 'text-center' : 'border-t border-gray-200 dark:border-gray-800 pt-8'}>
                    <p className="text-gray-600 dark:text-gray-400 text-sm text-center">
                        © {currentYear} CMS. All rights reserved.
                    </p>
                </div>
            </div>
        </footer>
    );
}
