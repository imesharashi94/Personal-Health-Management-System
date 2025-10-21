import { LineChart, Line, BarChart, Bar, AreaChart, Area, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer } from 'recharts';

export default function ChartCard({ title, data, type = 'line', dataKey = 'value', color = '#3b82f6' }) {
    const ChartComponent = {
        line: LineChart,
        bar: BarChart,
        area: AreaChart,
    }[type] || LineChart;

    const DataComponent = {
        line: Line,
        bar: Bar,
        area: Area,
    }[type] || Line;

    return (
        <div className="bg-white rounded-lg shadow p-6">
            <h3 className="text-lg font-medium text-gray-900 mb-4">{title}</h3>
            {data && data.length > 0 ? (
                <ResponsiveContainer width="100%" height={250}>
                    <ChartComponent data={data}>
                        <CartesianGrid strokeDasharray="3 3" />
                        <XAxis dataKey="date" tick={{ fontSize: 12 }} />
                        <YAxis tick={{ fontSize: 12 }} />
                        <Tooltip />
                        <DataComponent
                            type="monotone"
                            dataKey={dataKey}
                            stroke={color}
                            fill={type === 'area' ? color : undefined}
                            fillOpacity={type === 'area' ? 0.6 : undefined}
                        />
                    </ChartComponent>
                </ResponsiveContainer>
            ) : (
                <div className="h-64 flex items-center justify-center text-gray-500">
                    No data available
                </div>
            )}
        </div>
    );
}

