import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import KpiTile from '@/Components/KpiTile';
import ChartCard from '@/Components/ChartCard';

export default function Dashboard({ auth, kpis, charts, alerts, latestLabReport }) {
    return (
        <AuthenticatedLayout user={auth.user}>
            <Head title="Dashboard" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                    {/* Header */}
                    <div>
                        <h2 className="text-2xl font-bold text-gray-900">Health Dashboard</h2>
                        <p className="mt-1 text-sm text-gray-600">
                            Track your health metrics and stay informed about your wellbeing.
                        </p>
                    </div>

                    {/* Alerts */}
                    {alerts && alerts.length > 0 && (
                        <div className="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                            <div className="flex">
                                <div className="flex-shrink-0">
                                    <svg className="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fillRule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clipRule="evenodd" />
                                    </svg>
                                </div>
                                <div className="ml-3">
                                    <h3 className="text-sm font-medium text-yellow-800">Active Alerts</h3>
                                    <div className="mt-2 text-sm text-yellow-700 space-y-1">
                                        {alerts.map((alert) => (
                                            <p key={alert.id}>{alert.message}</p>
                                        ))}
                                    </div>
                                </div>
                            </div>
                        </div>
                    )}

                    {/* KPIs */}
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <KpiTile
                            title="Average Steps"
                            value={kpis?.steps?.['30d']}
                            unit="steps/day"
                            periods={kpis?.steps}
                        />
                        <KpiTile
                            title="Resting Heart Rate"
                            value={kpis?.hr?.['30d']}
                            unit="bpm"
                            periods={kpis?.hr}
                        />
                        <KpiTile
                            title="Average Sleep"
                            value={kpis?.sleep?.['30d']}
                            unit="hours/night"
                            periods={kpis?.sleep}
                        />
                    </div>

                    {/* Charts */}
                    <div className="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
                        <ChartCard title="Steps (14 days)" data={charts?.steps} type="bar" color="#3b82f6" />
                        <ChartCard title="Resting HR (14 days)" data={charts?.hr} type="line" color="#ef4444" />
                        <ChartCard title="Sleep (14 days)" data={charts?.sleep} type="area" color="#8b5cf6" />
                    </div>

                    {/* Latest Lab Report */}
                    {latestLabReport && (
                        <div className="bg-white rounded-lg shadow p-6">
                            <div className="flex justify-between items-center mb-4">
                                <h3 className="text-lg font-medium text-gray-900">Latest Lab Results</h3>
                                <a href={`/labs/${latestLabReport.id}`} className="text-sm text-blue-600 hover:text-blue-800">
                                    View Details →
                                </a>
                            </div>
                            <p className="text-sm text-gray-600 mb-4">
                                {latestLabReport.facility} • {new Date(latestLabReport.report_date).toLocaleDateString()}
                            </p>
                            <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
                                {latestLabReport.results?.slice(0, 4).map((result) => (
                                    <div key={result.id} className={`p-3 rounded ${result.flagged ? 'bg-red-50' : 'bg-gray-50'}`}>
                                        <p className="text-xs text-gray-600">{result.analyte}</p>
                                        <p className="text-lg font-semibold">{result.value} {result.unit}</p>
                                        {result.flagged && <span className="text-xs text-red-600">⚠ Flagged</span>}
                                    </div>
                                ))}
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
