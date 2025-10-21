export default function DateRangePicker({ startDate, endDate, onStartDateChange, onEndDateChange, label }) {
    return (
        <div className="flex flex-col gap-2">
            {label && <label className="text-sm font-medium text-gray-700">{label}</label>}
            <div className="flex gap-4">
                <div className="flex-1">
                    <label htmlFor="start-date" className="block text-xs text-gray-600 mb-1">
                        Start Date
                    </label>
                    <input
                        type="date"
                        id="start-date"
                        value={startDate}
                        onChange={(e) => onStartDateChange(e.target.value)}
                        className="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                    />
                </div>
                <div className="flex-1">
                    <label htmlFor="end-date" className="block text-xs text-gray-600 mb-1">
                        End Date
                    </label>
                    <input
                        type="date"
                        id="end-date"
                        value={endDate}
                        onChange={(e) => onEndDateChange(e.target.value)}
                        className="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                    />
                </div>
            </div>
        </div>
    );
}

