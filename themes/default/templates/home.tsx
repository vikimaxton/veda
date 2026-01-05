import React from 'react';
import Header from '../components/Header';
import Footer from '../components/Footer';
import BlockRenderer from '../components/BlockRenderer';

interface Page {
    id: string;
    title: string;
    slug: string;
    content_schema: Block[];
    seo_meta?: {
        title?: string;
        description?: string;
        og_image?: string;
    };
}

interface Block {
    type: string;
    attributes: Record<string, any>;
}

interface HomeTemplateProps {
    page: Page;
}

export default function HomeTemplate({ page }: HomeTemplateProps) {
    return (
        <div className="min-h-screen flex flex-col">
            <Header />

            <main className="flex-1">
                <div className="container mx-auto px-4 py-12">
                    <h1 className="text-4xl font-bold mb-8 text-gray-900 dark:text-white">
                        {page.title}
                    </h1>

                    <div className="prose prose-lg dark:prose-invert max-w-none">
                        <BlockRenderer blocks={page.content_schema} />
                    </div>
                </div>
            </main>

            <Footer />
        </div>
    );
}
