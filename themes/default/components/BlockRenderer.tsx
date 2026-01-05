import React from 'react';

interface Block {
    type: string;
    attributes: Record<string, any>;
}

interface BlockRendererProps {
    blocks: Block[];
    fullWidth?: boolean;
}

export default function BlockRenderer({ blocks, fullWidth = false }: BlockRendererProps) {
    const containerClass = fullWidth ? 'w-full' : 'container mx-auto px-4';

    const renderBlock = (block: Block, index: number) => {
        switch (block.type) {
            case 'heading':
                return renderHeading(block, index);
            case 'paragraph':
                return renderParagraph(block, index);
            case 'image':
                return renderImage(block, index);
            case 'button':
                return renderButton(block, index);
            case 'spacer':
                return renderSpacer(block, index);
            default:
                return null;
        }
    };

    const renderHeading = (block: Block, index: number) => {
        const { level = 2, content = '' } = block.attributes;
        const Tag = `h${level}` as keyof JSX.IntrinsicElements;
        const sizeClasses = {
            1: 'text-5xl font-bold',
            2: 'text-4xl font-bold',
            3: 'text-3xl font-semibold',
            4: 'text-2xl font-semibold',
            5: 'text-xl font-semibold',
            6: 'text-lg font-semibold',
        };

        return (
            <Tag
                key={index}
                className={`${sizeClasses[level as keyof typeof sizeClasses]} text-gray-900 dark:text-white mb-4`}
            >
                {content}
            </Tag>
        );
    };

    const renderParagraph = (block: Block, index: number) => {
        const { content = '' } = block.attributes;
        return (
            <p key={index} className="text-gray-700 dark:text-gray-300 mb-4 leading-relaxed">
                {content}
            </p>
        );
    };

    const renderImage = (block: Block, index: number) => {
        const { url = '', alt = '', caption = '' } = block.attributes;
        return (
            <figure key={index} className="mb-6">
                <img
                    src={url}
                    alt={alt}
                    className="w-full rounded-lg shadow-lg"
                />
                {caption && (
                    <figcaption className="text-sm text-gray-600 dark:text-gray-400 mt-2 text-center">
                        {caption}
                    </figcaption>
                )}
            </figure>
        );
    };

    const renderButton = (block: Block, index: number) => {
        const { text = 'Click me', url = '#', variant = 'primary' } = block.attributes;
        const variantClasses = {
            primary: 'bg-blue-600 hover:bg-blue-700 text-white',
            secondary: 'bg-purple-600 hover:bg-purple-700 text-white',
            outline: 'border-2 border-gray-900 dark:border-white text-gray-900 dark:text-white hover:bg-gray-900 hover:text-white dark:hover:bg-white dark:hover:text-gray-900',
        };

        return (
            <div key={index} className="mb-6">
                <a
                    href={url}
                    className={`inline-block px-6 py-3 rounded-lg font-semibold transition-colors ${variantClasses[variant as keyof typeof variantClasses]}`}
                >
                    {text}
                </a>
            </div>
        );
    };

    const renderSpacer = (block: Block, index: number) => {
        const { height = 40 } = block.attributes;
        return <div key={index} style={{ height: `${height}px` }} />;
    };

    return (
        <div className={containerClass}>
            {blocks.map((block, index) => renderBlock(block, index))}
        </div>
    );
}
