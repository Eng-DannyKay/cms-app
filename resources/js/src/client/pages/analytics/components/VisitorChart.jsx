import React from 'react';
import { LineChart, Line, XAxis, YAxis, Tooltip, ResponsiveContainer, CartesianGrid } from 'recharts';
import { Users } from 'lucide-react';

const VisitorChart = ({ data = [], period }) => {
  if (!data.length) {
    return <div className="p-6 bg-white dark:bg-gray-800 rounded-2xl">No visitor data</div>;
  }

  return (
    <div className="p-6 bg-white dark:bg-gray-800 rounded-2xl">
      <div className="flex items-center gap-2 mb-4">
        <Users size={20} className="text-gray-700 dark:text-gray-300" />
        <h3 className="text-lg font-semibold">Visitors ({period})</h3>
      </div>
      <ResponsiveContainer width="100%" height={300}>
        <LineChart data={data}>
          <CartesianGrid strokeDasharray="3 3" className="stroke-gray-200 dark:stroke-gray-700" />
          <XAxis dataKey="date" />
          <YAxis />
          <Tooltip />
          <Line type="monotone" dataKey="visitors" stroke="#3b82f6" strokeWidth={2} />
        </LineChart>
      </ResponsiveContainer>
    </div>
  );
};

export default VisitorChart;
