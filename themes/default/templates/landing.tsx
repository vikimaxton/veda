import React from 'react';
import Header from '../components/Header';
import Footer from '../components/Footer';
import BlockRenderer from '../components/BlockRenderer';

interface Page {
    id: string;
    title: string;
    slug: string;
    content_schema: Block[];
}

interface Block {
    type: string;
    attributes: Record<string, any>;
}

interface LandingTemplateProps {
    page: Page;
}

export default function LandingTemplate({ page }: LandingTemplateProps) {
    return (
        <div className="min-h-screen">
            <Header minimal />

            <main>
                <BlockRenderer blocks={page.content_schema} />
            </main>

            <Footer minimal />
        </div>
    );
}
