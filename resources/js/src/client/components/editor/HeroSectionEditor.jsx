import React from 'react';

const HeroSectionEditor = ({ section, onChange }) => {
  return (
    <div className="space-y-2">
      <input
        type="text"
        value={section.title}
        onChange={(e) => onChange({ ...section, title: e.target.value })}
        placeholder="Hero Title"
        className="w-full border p-2"
      />
      <input
        type="text"
        value={section.subtitle}
        onChange={(e) => onChange({ ...section, subtitle: e.target.value })}
        placeholder="Subtitle"
        className="w-full border p-2"
      />
      <input
        type="text"
        value={section.button_text}
        onChange={(e) => onChange({ ...section, button_text: e.target.value })}
        placeholder="Button Text"
        className="w-full border p-2"
      />
      <input
        type="text"
        value={section.button_link}
        onChange={(e) => onChange({ ...section, button_link: e.target.value })}
        placeholder="Button Link"
        className="w-full border p-2"
      />
    </div>
  );
};

export default HeroSectionEditor;
