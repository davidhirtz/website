import {emit} from "./subscribers.ts";
import {saveCategoriesToCookie} from "./cookies.ts";

export type Categories = Set<string>;

let categories: Categories = new Set();

export const getCategories = () => categories;

export const setCategories = (commaSeparatedCategories: string, fromCookie: boolean = false) => {
    commaSeparatedCategories.split(',').forEach((category) => categories.add(category));

    if (!fromCookie) {
        saveCategoriesToCookie(categories);
    }

    emit(fromCookie);
}
