import React from 'react';
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

interface FullWidthTemplateProps {
    page: Page;
}

export default function FullWidthTemplate({ page }: FullWidthTemplateProps) {
    return (
        <div className="min-h-screen">
            <BlockRenderer blocks={page.content_schema} fullWidth />
        </div>
    );
}
