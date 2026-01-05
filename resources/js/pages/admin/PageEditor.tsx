import { Head, Link, router, useForm } from '@inertiajs/react';
import { useState } from 'react';

interface Block {
    id: string;
    type: 'heading' | 'paragraph' | 'image' | 'button' | 'spacer';
    data: any;
}

interface Page {
    id?: string;
    title: string;
    slug: string;
    status: 'draft' | 'published' | 'private';
    template: string;
    content_schema: Block[];
    seo_meta: {
        title?: string;
        description?: string;
        keywords?: string;
    };
    parent_id?: string | null;
}

interface PageEditorProps {
    auth: {
        user: {
            name: string;
            email: string;
        };
    };
    page: Page | null;
    templates: string[];
}

export default function PageEditor({ auth, page, templates }: PageEditorProps) {
    const { data, setData, processing, errors } = useForm({
        title: page?.title || '',
        slug: page?.slug || '',
        status: page?.status || 'draft',
        template: page?.template || 'home',
        content_schema: page?.content_schema || [],
        seo_meta: page?.seo_meta || {},
        parent_id: page?.parent_id || null,
    });

    const [showSEO, setShowSEO] = useState(false);

    const generateSlug = (title: string) => {
        return title
            .toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/(^-|-$)/g, '');
    };

    const handleTitleChange = (title: string) => {
        setData('title', title);
        if (!page) {
            setData('slug', generateSlug(title));
        }
    };

    const addBlock = (type: Block['type']) => {
        const newBlock: Block = {
            id: `block-${Date.now()}`,
            type,
            data: getDefaultBlockData(type),
        };
        setData('content_schema', [...data.content_schema, newBlock]);
    };

    const getDefaultBlockData = (type: Block['type']) => {
        switch (type) {
            case 'heading':
                return { level: 'h2', text: 'New Heading', alignment: 'left' };
            case 'paragraph':
                return { text: 'Enter your text here...', alignment: 'left' };
            case 'image':
                return { url: '', alt: '', width: '100%', alignment: 'center' };
            case 'button':
                return { text: 'Click me', url: '#', style: 'primary', alignment: 'left' };
            case 'spacer':
                return { height: '40px' };
            default:
                return {};
        }
    };

    const updateBlock = (id: string, newData: any) => {
        setData(
            'content_schema',
            data.content_schema.map((block) =>
                block.id === id ? { ...block, data: newData } : block
            )
        );
    };

    const deleteBlock = (id: string) => {
        setData(
            'content_schema',
            data.content_schema.filter((block) => block.id !== id)
        );
    };

    const moveBlock = (id: string, direction: 'up' | 'down') => {
        const index = data.content_schema.findIndex((block) => block.id === id);
        if (
            (direction === 'up' && index === 0) ||
            (direction === 'down' && index === data.content_schema.length - 1)
        ) {
            return;
        }

        const newBlocks = [...data.content_schema];
        const targetIndex = direction === 'up' ? index - 1 : index + 1;
        [newBlocks[index], newBlocks[targetIndex]] = [newBlocks[targetIndex], newBlocks[index]];
        setData('content_schema', newBlocks);
    };

    const handleSubmit = (status: 'draft' | 'published') => {
        const submitData = { ...data, status };

        if (page) {
            router.put(`/api/cms/pages/${page.id}`, submitData as any, {
                onSuccess: () => router.visit('/admin/pages'),
            });
        } else {
            router.post('/api/cms/pages', submitData as any, {
                onSuccess: () => router.visit('/admin/pages'),
            });
        }
    };

    return (
        <>
            <Head title={page ? `Edit ${page.title}` : 'Create New Page'} />

            <div className="min-h-screen bg-gray-50 dark:bg-gray-900">
                <header className="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <div className="px-4 sm:px-6 lg:px-8">
                        <div className="flex h-16 items-center justify-between">
                            <div className="flex items-center gap-4">
                                <Link href="/admin" className="text-2xl font-bold text-gray-900 dark:text-white hover:text-gray-700">
                                    CMS Admin
                                </Link>
                                <span className="text-gray-400">/</span>
                                <Link href="/admin/pages" className="text-gray-600 dark:text-gray-300 hover:text-gray-900">
                                    Pages
                                </Link>
                                <span className="text-gray-400">/</span>
                                <span className="text-gray-900 dark:text-white">{page ? 'Edit' : 'Create'}</span>
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

                <div className="max-w-7xl mx-auto p-8">
                    <div className="grid grid-cols-3 gap-8">
                        {/* Main Editor */}
                        <div className="col-span-2 space-y-6">
                            {/* Title */}
                            <div className="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                                <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Page Title
                                </label>
                                <input
                                    type="text"
                                    value={data.title}
                                    onChange={(e) => handleTitleChange(e.target.value)}
                                    className="w-full px-4 py-3 text-2xl font-bold border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="Enter page title..."
                                />
                                {errors.title && <p className="mt-2 text-sm text-red-600">{errors.title}</p>}
                            </div>

                            {/* Content Blocks */}
                            <div className="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                                <div className="flex items-center justify-between mb-4">
                                    <h3 className="text-lg font-semibold text-gray-900 dark:text-white">Content</h3>
                                    <div className="relative group">
                                        <button
                                            type="button"
                                            className="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors"
                                        >
                                            <svg className="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 4v16m8-8H4" />
                                            </svg>
                                            Add Block
                                        </button>
                                        <div className="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-700 rounded-lg shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-10">
                                            <button onClick={() => addBlock('heading')} className="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 rounded-t-lg">
                                                Heading
                                            </button>
                                            <button onClick={() => addBlock('paragraph')} className="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                                                Paragraph
                                            </button>
                                            <button onClick={() => addBlock('image')} className="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                                                Image
                                            </button>
                                            <button onClick={() => addBlock('button')} className="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                                                Button
                                            </button>
                                            <button onClick={() => addBlock('spacer')} className="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 rounded-b-lg">
                                                Spacer
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                {data.content_schema.length === 0 ? (
                                    <div className="text-center py-12 text-gray-500 dark:text-gray-400">
                                        No content blocks yet. Click "Add Block" to get started!
                                    </div>
                                ) : (
                                    <div className="space-y-4">
                                        {data.content_schema.map((block, index) => (
                                            <BlockEditor
                                                key={block.id}
                                                block={block}
                                                index={index}
                                                total={data.content_schema.length}
                                                onUpdate={(newData) => updateBlock(block.id, newData)}
                                                onDelete={() => deleteBlock(block.id)}
                                                onMove={(direction) => moveBlock(block.id, direction)}
                                            />
                                        ))}
                                    </div>
                                )}
                            </div>

                            {/* SEO Section */}
                            <div className="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                                <button
                                    type="button"
                                    onClick={() => setShowSEO(!showSEO)}
                                    className="flex items-center justify-between w-full text-left"
                                >
                                    <h3 className="text-lg font-semibold text-gray-900 dark:text-white">SEO Settings</h3>
                                    <svg
                                        className={`w-5 h-5 text-gray-500 transition-transform ${showSEO ? 'rotate-180' : ''}`}
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>

                                {showSEO && (
                                    <div className="mt-4 space-y-4">
                                        <div>
                                            <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                Meta Title
                                            </label>
                                            <input
                                                type="text"
                                                value={data.seo_meta.title || ''}
                                                onChange={(e) => setData('seo_meta', { ...data.seo_meta, title: e.target.value })}
                                                className="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                                placeholder="SEO title..."
                                            />
                                        </div>
                                        <div>
                                            <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                Meta Description
                                            </label>
                                            <textarea
                                                value={data.seo_meta.description || ''}
                                                onChange={(e) => setData('seo_meta', { ...data.seo_meta, description: e.target.value })}
                                                rows={3}
                                                className="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                                placeholder="SEO description..."
                                            />
                                        </div>
                                        <div>
                                            <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                Keywords
                                            </label>
                                            <input
                                                type="text"
                                                value={data.seo_meta.keywords || ''}
                                                onChange={(e) => setData('seo_meta', { ...data.seo_meta, keywords: e.target.value })}
                                                className="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                                placeholder="keyword1, keyword2, keyword3"
                                            />
                                        </div>
                                    </div>
                                )}
                            </div>
                        </div>

                        {/* Sidebar */}
                        <div className="space-y-6">
                            {/* Publish */}
                            <div className="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                                <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">Publish</h3>
                                <div className="space-y-4">
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Status
                                        </label>
                                        <select
                                            value={data.status}
                                            onChange={(e) => setData('status', e.target.value as any)}
                                            className="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                        >
                                            <option value="draft">Draft</option>
                                            <option value="published">Published</option>
                                            <option value="private">Private</option>
                                        </select>
                                    </div>

                                    <div className="flex gap-2">
                                        <button
                                            type="button"
                                            onClick={() => handleSubmit('draft')}
                                            disabled={processing}
                                            className="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50"
                                        >
                                            Save Draft
                                        </button>
                                        <button
                                            type="button"
                                            onClick={() => handleSubmit('published')}
                                            disabled={processing}
                                            className="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg disabled:opacity-50"
                                        >
                                            Publish
                                        </button>
                                    </div>
                                </div>
                            </div>

                            {/* Page Settings */}
                            <div className="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                                <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">Page Settings</h3>
                                <div className="space-y-4">
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Slug
                                        </label>
                                        <input
                                            type="text"
                                            value={data.slug}
                                            onChange={(e) => setData('slug', e.target.value)}
                                            className="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                        />
                                        {errors.slug && <p className="mt-1 text-sm text-red-600">{errors.slug}</p>}
                                    </div>

                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Template
                                        </label>
                                        <select
                                            value={data.template}
                                            onChange={(e) => setData('template', e.target.value)}
                                            className="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                        >
                                            {templates.map((template) => (
                                                <option key={template} value={template}>
                                                    {template.charAt(0).toUpperCase() + template.slice(1)}
                                                </option>
                                            ))}
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}

// Block Editor Component
function BlockEditor({
    block,
    index,
    total,
    onUpdate,
    onDelete,
    onMove,
}: {
    block: Block;
    index: number;
    total: number;
    onUpdate: (data: any) => void;
    onDelete: () => void;
    onMove: (direction: 'up' | 'down') => void;
}) {
    const renderBlockEditor = () => {
        switch (block.type) {
            case 'heading':
                return (
                    <div className="space-y-3">
                        <div className="flex gap-3">
                            <select
                                value={block.data.level}
                                onChange={(e) => onUpdate({ ...block.data, level: e.target.value })}
                                className="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                            >
                                <option value="h1">H1</option>
                                <option value="h2">H2</option>
                                <option value="h3">H3</option>
                                <option value="h4">H4</option>
                                <option value="h5">H5</option>
                                <option value="h6">H6</option>
                            </select>
                            <select
                                value={block.data.alignment}
                                onChange={(e) => onUpdate({ ...block.data, alignment: e.target.value })}
                                className="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                            >
                                <option value="left">Left</option>
                                <option value="center">Center</option>
                                <option value="right">Right</option>
                            </select>
                        </div>
                        <input
                            type="text"
                            value={block.data.text}
                            onChange={(e) => onUpdate({ ...block.data, text: e.target.value })}
                            className="w-full px-4 py-2 text-xl font-bold border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                            placeholder="Heading text..."
                        />
                    </div>
                );

            case 'paragraph':
                return (
                    <div className="space-y-3">
                        <select
                            value={block.data.alignment}
                            onChange={(e) => onUpdate({ ...block.data, alignment: e.target.value })}
                            className="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                        >
                            <option value="left">Left</option>
                            <option value="center">Center</option>
                            <option value="right">Right</option>
                        </select>
                        <textarea
                            value={block.data.text}
                            onChange={(e) => onUpdate({ ...block.data, text: e.target.value })}
                            rows={4}
                            className="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                            placeholder="Paragraph text..."
                        />
                    </div>
                );

            case 'image':
                return (
                    <div className="space-y-3">
                        <input
                            type="text"
                            value={block.data.url}
                            onChange={(e) => onUpdate({ ...block.data, url: e.target.value })}
                            className="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                            placeholder="Image URL..."
                        />
                        <input
                            type="text"
                            value={block.data.alt}
                            onChange={(e) => onUpdate({ ...block.data, alt: e.target.value })}
                            className="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                            placeholder="Alt text..."
                        />
                        <div className="flex gap-3">
                            <input
                                type="text"
                                value={block.data.width}
                                onChange={(e) => onUpdate({ ...block.data, width: e.target.value })}
                                className="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                placeholder="Width (e.g., 100%, 500px)"
                            />
                            <select
                                value={block.data.alignment}
                                onChange={(e) => onUpdate({ ...block.data, alignment: e.target.value })}
                                className="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                            >
                                <option value="left">Left</option>
                                <option value="center">Center</option>
                                <option value="right">Right</option>
                            </select>
                        </div>
                    </div>
                );

            case 'button':
                return (
                    <div className="space-y-3">
                        <input
                            type="text"
                            value={block.data.text}
                            onChange={(e) => onUpdate({ ...block.data, text: e.target.value })}
                            className="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                            placeholder="Button text..."
                        />
                        <input
                            type="text"
                            value={block.data.url}
                            onChange={(e) => onUpdate({ ...block.data, url: e.target.value })}
                            className="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                            placeholder="Button URL..."
                        />
                        <div className="flex gap-3">
                            <select
                                value={block.data.style}
                                onChange={(e) => onUpdate({ ...block.data, style: e.target.value })}
                                className="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                            >
                                <option value="primary">Primary</option>
                                <option value="secondary">Secondary</option>
                                <option value="outline">Outline</option>
                            </select>
                            <select
                                value={block.data.alignment}
                                onChange={(e) => onUpdate({ ...block.data, alignment: e.target.value })}
                                className="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                            >
                                <option value="left">Left</option>
                                <option value="center">Center</option>
                                <option value="right">Right</option>
                            </select>
                        </div>
                    </div>
                );

            case 'spacer':
                return (
                    <div>
                        <input
                            type="text"
                            value={block.data.height}
                            onChange={(e) => onUpdate({ ...block.data, height: e.target.value })}
                            className="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                            placeholder="Height (e.g., 40px, 2rem)"
                        />
                    </div>
                );

            default:
                return null;
        }
    };

    return (
        <div className="border border-gray-300 dark:border-gray-600 rounded-lg p-4 bg-gray-50 dark:bg-gray-700">
            <div className="flex items-center justify-between mb-3">
                <span className="text-sm font-medium text-gray-700 dark:text-gray-300 capitalize">
                    {block.type} Block
                </span>
                <div className="flex gap-2">
                    <button
                        type="button"
                        onClick={() => onMove('up')}
                        disabled={index === 0}
                        className="p-1 text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white disabled:opacity-30"
                    >
                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 15l7-7 7 7" />
                        </svg>
                    </button>
                    <button
                        type="button"
                        onClick={() => onMove('down')}
                        disabled={index === total - 1}
                        className="p-1 text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white disabled:opacity-30"
                    >
                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <button
                        type="button"
                        onClick={onDelete}
                        className="p-1 text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                    >
                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>
            </div>
            {renderBlockEditor()}
        </div>
    );
}
