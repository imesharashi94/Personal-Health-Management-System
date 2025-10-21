import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import DataTable from '@/Components/DataTable';

export default function LabShow({ auth, report }) {
    const columns = [
        { header: 'Analyte', accessor: 'analyte' },
        { header: 'Value', accessor: 'value' },
        { header: 'Unit', accessor: 'unit' },
        {
            header: 'Reference Range',
            render: (row) => `${row.ref_low} - ${row.ref_high}`
        },
        {
            header: 'Status',
            render: (row) => (
                <span className={`px-2 py-1 text-xs font-medium rounded ${
                    row.flagged ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'
                }`}>
                    {row.flagged ? 'Abnormal' : 'Normal'}
                </span>
            )
        },
    ];

    return (
        <AuthenticatedLayout user={auth.user}>
            <Head title={`Lab Report - ${report.facility || 'Details'}`} />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                    <div>
                        <a href="/labs" className="text-sm text-blue-600 hover:text-blue-800 mb-2 inline-block">
                            ← Back to Lab Reports
                        </a>
                        <h2 className="text-2xl font-bold text-gray-900">{report.facility || 'Lab Report'}</h2>
                        <p className="mt-1 text-sm text-gray-600">
                            Report Date: {report.report_date ? new Date(report.report_date).toLocaleDateString() : 'N/A'}
                            {' • '}
                            Status: <span className="capitalize">{report.status}</span>
                        </p>
                    </div>

                    <div className="bg-white rounded-lg shadow overflow-hidden">
                        <DataTable
                            columns={columns}
                            data={report.results}
                            emptyMessage="No results available for this report."
                        />
                    </div>

                    {report.status === 'failed' && (
                        <div className="bg-red-50 border border-red-200 rounded-lg p-4">
                            <p className="text-sm text-red-800">
                                OCR parsing failed for this report. Please try uploading a clearer image or PDF.
                            </p>
                        </div>
                    )}
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

