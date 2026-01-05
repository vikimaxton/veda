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

interface BlankTemplateProps {
    page: Page;
}

export default function BlankTemplate({ page }: BlankTemplateProps) {
    return (
        <BlockRenderer blocks={page.content_schema} fullWidth />
    );
}
