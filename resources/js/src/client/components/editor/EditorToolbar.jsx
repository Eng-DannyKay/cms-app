
const EditorToolbar = ({ editor }) => {
    if (!editor) return null;

    const addImage = () => {
        const url = window.prompt('Enter image URL');
        if (url) {
            editor.chain().focus().setImage({ src: url }).run();
        }
    };

    const setLink = () => {
        const url = window.prompt('Enter URL');
        if (url === null) return;
        if (url === '') {
            editor.chain().focus().unsetLink().run();
            return;
        }
        editor.chain().focus().setLink({ href: url }).run();
    };

    return (
        <div className="flex flex-wrap items-center gap-1 p-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 rounded-t-lg">
            {/* Text formatting */}
            <button
                onClick={() => editor.chain().focus().toggleBold().run()}
                className={`p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700 ${
                    editor.isActive('bold') ? 'bg-gray-200 dark:bg-gray-700' : ''
                }`}
                title="Bold"
            >
                <strong>B</strong>
            </button>

            <button
                onClick={() => editor.chain().focus().toggleItalic().run()}
                className={`p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700 ${
                    editor.isActive('italic') ? 'bg-gray-200 dark:bg-gray-700' : ''
                }`}
                title="Italic"
            >
                <em>I</em>
            </button>

            <button
                onClick={() => editor.chain().focus().toggleStrike().run()}
                className={`p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700 ${
                    editor.isActive('strike') ? 'bg-gray-200 dark:bg-gray-700' : ''
                }`}
                title="Strike"
            >
                <s>S</s>
            </button>

            {/* Headings */}
            <button
                onClick={() => editor.chain().focus().setParagraph().run()}
                className={`p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700 ${
                    editor.isActive('paragraph') ? 'bg-gray-200 dark:bg-gray-700' : ''
                }`}
                title="Paragraph"
            >
                P
            </button>

            <button
                onClick={() => editor.chain().focus().toggleHeading({ level: 2 }).run()}
                className={`p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700 ${
                    editor.isActive('heading', { level: 2 }) ? 'bg-gray-200 dark:bg-gray-700' : ''
                }`}
                title="Heading 2"
            >
                H2
            </button>

            <button
                onClick={() => editor.chain().focus().toggleHeading({ level: 3 }).run()}
                className={`p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700 ${
                    editor.isActive('heading', { level: 3 }) ? 'bg-gray-200 dark:bg-gray-700' : ''
                }`}
                title="Heading 3"
            >
                H3
            </button>

            {/* Lists */}
            <button
                onClick={() => editor.chain().focus().toggleBulletList().run()}
                className={`p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700 ${
                    editor.isActive('bulletList') ? 'bg-gray-200 dark:bg-gray-700' : ''
                }`}
                title="Bullet List"
            >
                • List
            </button>

            <button
                onClick={() => editor.chain().focus().toggleOrderedList().run()}
                className={`p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700 ${
                    editor.isActive('orderedList') ? 'bg-gray-200 dark:bg-gray-700' : ''
                }`}
                title="Numbered List"
            >
                1. List
            </button>

            {/* Media */}
            <button
                onClick={addImage}
                className="p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700"
                title="Add Image"
            >
                📷
            </button>

            <button
                onClick={setLink}
                className={`p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700 ${
                    editor.isActive('link') ? 'bg-gray-200 dark:bg-gray-700' : ''
                }`}
                title="Add Link"
            >
                🔗
            </button>

            {/* Alignment */}
            <button
                onClick={() => editor.chain().focus().setTextAlign('left').run()}
                className={`p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700 ${
                    editor.isActive({ textAlign: 'left' }) ? 'bg-gray-200 dark:bg-gray-700' : ''
                }`}
                title="Align Left"
            >
                ←
            </button>

            <button
                onClick={() => editor.chain().focus().setTextAlign('center').run()}
                className={`p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700 ${
                    editor.isActive({ textAlign: 'center' }) ? 'bg-gray-200 dark:bg-gray-700' : ''
                }`}
                title="Align Center"
            >
                ↔
            </button>

            <button
                onClick={() => editor.chain().focus().setTextAlign('right').run()}
                className={`p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700 ${
                    editor.isActive({ textAlign: 'right' }) ? 'bg-gray-200 dark:bg-gray-700' : ''
                }`}
                title="Align Right"
            >
                →
            </button>
        </div>
    );
};

export default EditorToolbar;
