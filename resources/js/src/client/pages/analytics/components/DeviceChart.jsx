import { HelpCircle, Laptop, Monitor, Smartphone, TabletSmartphone } from 'lucide-react';
import { Bar, BarChart, CartesianGrid, ResponsiveContainer, Tooltip, XAxis, YAxis } from 'recharts';

const DeviceChart = ({ data = [] }) => {
  if (!data.length) {
    return <div className="p-6 bg-white dark:bg-gray-800 rounded-2xl">No device data</div>;
  }


  const CustomTooltip = ({ active, payload }) => {
    if (active && payload && payload.length) {
      const deviceType = payload[0]?.payload?.device?.toLowerCase();
      let DeviceIcon = HelpCircle;
      if (deviceType?.includes('mobile') || deviceType?.includes('phone')) {
        DeviceIcon = Smartphone;
      } else if (deviceType?.includes('desktop') || deviceType?.includes('pc')) {
        DeviceIcon = Monitor;
      } else if (deviceType?.includes('tablet')) {
        DeviceIcon = TabletSmartphone;
      } else if (deviceType?.includes('laptop')) {
        DeviceIcon = Laptop;
      }

      return (
        <div className="bg-white dark:bg-gray-800 p-2 border border-gray-200 dark:border-gray-700 rounded-md shadow-md">
          <div className="flex items-center gap-2">
            <DeviceIcon size={16} />
            <span className="font-medium">{payload[0].payload.device}</span>
          </div>
          <p className="text-sm">{`Count: ${payload[0].value}`}</p>
        </div>
      );
    }
    return null;
  };

  return (
    <div className="p-6 bg-white dark:bg-gray-800 rounded-2xl">
      <div className="flex items-center gap-2 mb-4">
        <Monitor size={20} className="text-gray-700 dark:text-gray-300" />
        <h3 className="text-lg font-semibold">Devices</h3>
      </div>
      <ResponsiveContainer width="100%" height={300}>
        <BarChart data={data}>
          <CartesianGrid strokeDasharray="3 3" className="stroke-gray-200 dark:stroke-gray-700" />
          <XAxis dataKey="device" />
          <YAxis />
          <Tooltip content={<CustomTooltip />} />
          <Bar dataKey="count" fill="#10b981" />
        </BarChart>
      </ResponsiveContainer>
    </div>
  );
};

export default DeviceChart;
