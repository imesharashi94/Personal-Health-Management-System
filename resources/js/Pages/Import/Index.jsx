import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, router } from '@inertiajs/react';
import { useState } from 'react';
import FileDropzone from '@/Components/FileDropzone';

export default function ImportIndex({ auth }) {
    const [uploading, setUploading] = useState(false);
    const [selectedFile, setSelectedFile] = useState(null);

    const handleDrop = (acceptedFiles) => {
        if (acceptedFiles && acceptedFiles[0]) {
            setSelectedFile(acceptedFiles[0]);
        }
    };

    const handleUpload = () => {
        if (!selectedFile) return;

        setUploading(true);
        const formData = new FormData();
        formData.append('file', selectedFile);

        router.post('/import/upload', formData, {
            onFinish: () => {
                setUploading(false);
                setSelectedFile(null);
            },
        });
    };

    return (
        <AuthenticatedLayout user={auth.user}>
            <Head title="Import Data" />

            <div className="py-12">
                <div className="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
                    <div>
                        <h2 className="text-2xl font-bold text-gray-900">Import Health Data</h2>
                        <p className="mt-1 text-sm text-gray-600">
                            Upload CSV or JSON files from Google Fit, Health Connect, or other sources.
                        </p>
                    </div>

                    <div className="bg-white rounded-lg shadow p-6 space-y-6">
                        <div>
                            <h3 className="text-lg font-medium mb-2">Upload File</h3>
                            <p className="text-sm text-gray-600 mb-4">
                                Your file should contain health metrics with columns: date, metric (steps/hr/sleep), value, and unit.
                            </p>
                            <FileDropzone
                                onDrop={handleDrop}
                                accept={{ 'text/csv': ['.csv'], 'application/json': ['.json'], 'text/plain': ['.txt'] }}
                            />
                            {selectedFile && (
                                <div className="mt-4 flex items-center justify-between p-4 bg-gray-50 rounded">
                                    <span className="text-sm text-gray-700">
                                        Selected: {selectedFile.name} ({(selectedFile.size / 1024).toFixed(2)} KB)
                                    </span>
                                    <button
                                        onClick={() => setSelectedFile(null)}
                                        className="text-sm text-red-600 hover:text-red-800"
                                    >
                                        Remove
                                    </button>
                                </div>
                            )}
                        </div>

                        {selectedFile && (
                            <div className="flex justify-end">
                                <button
                                    onClick={handleUpload}
                                    disabled={uploading}
                                    className="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50"
                                >
                                    {uploading ? 'Importing...' : 'Import Data'}
                                </button>
                            </div>
                        )}
                    </div>

                    <div className="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h4 className="text-sm font-medium text-blue-900 mb-2">CSV Format Example</h4>
                        <pre className="text-xs text-blue-800 bg-white p-3 rounded overflow-x-auto">
{`date,metric,value,unit
2025-10-01,steps,8500,steps
2025-10-01,hr,68,bpm
2025-10-01,sleep,7.5,hours`}
                        </pre>
                    </div>

                    <div className="bg-gray-50 rounded-lg p-6">
                        <h3 className="text-lg font-medium mb-4">How It Works</h3>
                        <ol className="list-decimal list-inside space-y-2 text-sm text-gray-700">
                            <li>Export your health data from Google Fit, Apple Health, or other apps as CSV/JSON</li>
                            <li>Upload the file using the form above</li>
                            <li>Your data will be processed in the background</li>
                            <li>Once complete, you'll see it reflected in your dashboard charts</li>
                        </ol>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

