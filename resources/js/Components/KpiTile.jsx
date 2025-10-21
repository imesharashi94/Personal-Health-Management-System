export default function KpiTile({ title, value, unit, periods = {} }) {
    return (
        <div className="bg-white rounded-lg shadow p-6">
            <h3 className="text-sm font-medium text-gray-500 uppercase">{title}</h3>
            <div className="mt-2 flex items-baseline">
                <p className="text-3xl font-semibold text-gray-900">
                    {value !== null && value !== undefined ? value.toLocaleString() : '—'}
                </p>
                {unit && <span className="ml-2 text-sm text-gray-500">{unit}</span>}
            </div>
            {periods && Object.keys(periods).length > 0 && (
                <div className="mt-4 flex gap-4 text-sm">
                    {Object.entries(periods).map(([period, val]) => (
                        <div key={period}>
                            <span className="text-gray-500">{period}: </span>
                            <span className="font-medium text-gray-900">{val?.toLocaleString() || '—'}</span>
                        </div>
                    ))}
                </div>
            )}
        </div>
    );
}

