import React from 'react';

const FeaturesSectionEditor = ({ section, onChange }) => {
  const updateItem = (i, field, value) => {
    const items = [...section.items];
    items[i][field] = value;
    onChange({ ...section, items });
  };

  return (
    <div>
      {section.items.map((item, i) => (
        <div key={i} className="border p-2 mb-2">
          <input
            type="text"
            value={item.title}
            onChange={(e) => updateItem(i, 'title', e.target.value)}
            placeholder="Feature title"
            className="w-full border p-1 mb-1"
          />
          <textarea
            value={item.description}
            onChange={(e) => updateItem(i, 'description', e.target.value)}
            placeholder="Description"
            className="w-full border p-1 mb-1"
          />
          <input
            type="text"
            value={item.icon}
            onChange={(e) => updateItem(i, 'icon', e.target.value)}
            placeholder="Icon"
            className="w-full border p-1"
          />
        </div>
      ))}
    </div>
  );
};

export default FeaturesSectionEditor;
