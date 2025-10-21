import { useEffect, useState } from 'react';
import { XMarkIcon } from '@heroicons/react/24/outline';

export default function Toast({ message, type = 'success', show, onClose }) {
    const [visible, setVisible] = useState(show);

    useEffect(() => {
        setVisible(show);
        if (show) {
            const timer = setTimeout(() => {
                setVisible(false);
                if (onClose) onClose();
            }, 5000);
            return () => clearTimeout(timer);
        }
    }, [show, onClose]);

    if (!visible) return null;

    const bgColor = {
        success: 'bg-green-50 border-green-200',
        error: 'bg-red-50 border-red-200',
        warning: 'bg-yellow-50 border-yellow-200',
        info: 'bg-blue-50 border-blue-200',
    }[type] || 'bg-gray-50 border-gray-200';

    const textColor = {
        success: 'text-green-800',
        error: 'text-red-800',
        warning: 'text-yellow-800',
        info: 'text-blue-800',
    }[type] || 'text-gray-800';

    return (
        <div className={`fixed top-4 right-4 z-50 max-w-sm w-full border ${bgColor} rounded-lg shadow-lg p-4`}>
            <div className="flex items-start">
                <div className="flex-1">
                    <p className={`text-sm font-medium ${textColor}`}>{message}</p>
                </div>
                <button
                    onClick={() => {
                        setVisible(false);
                        if (onClose) onClose();
                    }}
                    className={`ml-4 ${textColor} hover:opacity-75`}
                >
                    <XMarkIcon className="h-5 w-5" />
                </button>
            </div>
        </div>
    );
}

