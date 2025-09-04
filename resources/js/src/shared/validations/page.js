import { z } from 'zod';

export const pageSchema = z.object({
    title: z.string().min(1, 'Title is required').max(255, 'Title too long'),
    slug: z.string()
        .min(1, 'Slug is required')
        .max(255, 'Slug too long')
        .regex(/^[a-z0-9]+(?:-[a-z0-9]+)*$/, 'Slug can only contain lowercase letters, numbers, and hyphens'),
    content: z.object({
        sections: z.array(z.any()).optional(),
    }).optional(),
    is_published: z.boolean().default(false),
});

export const pageContentSchema = z.object({
    sections: z.array(z.object({
        type: z.string(),
        content: z.any(),
    })),
});