import { Cell, Pie, PieChart, ResponsiveContainer, Tooltip } from 'recharts';

const COLORS = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#6366f1'];

const GeographyChart = ({ data = [] }) => {
  if (!data.length) {
    return <div className="p-6 bg-white dark:bg-gray-800 rounded-2xl">No geography data</div>;
  }

  return (
    <div className="p-6 bg-white dark:bg-gray-800 rounded-2xl">
      <h3 className="text-lg font-semibold mb-4">Visitors by Country</h3>
      <ResponsiveContainer width="100%" height={300}>
        <PieChart>
          <Pie
            data={data}
            cx="50%"
            cy="50%"
            labelLine={false}
            outerRadius={120}
            fill="#8884d8"
            dataKey="value"
          >
            {data.map((_, index) => (
              <Cell key={index} fill={COLORS[index % COLORS.length]} />
            ))}
          </Pie>
          <Tooltip />
        </PieChart>
      </ResponsiveContainer>
      <div className="mt-4">
        {data.map((entry, index) => (
          <div key={index} className="flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-300">
            <span className="inline-block w-3 h-3 rounded-full" style={{ background: COLORS[index % COLORS.length] }} />
            <span>{entry.country}</span>
            <span className="ml-auto font-medium">{entry.value}</span>
          </div>
        ))}
      </div>
    </div>
  );
};

export default GeographyChart;
