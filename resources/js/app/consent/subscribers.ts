import {Categories, getCategories} from "./categories.ts";

type Subscriber = (categories: Categories, fromCookie: boolean) => void;

const subscribers: Set<Subscriber> = new Set();

export const subscribe = (subscriber: Subscriber) => {
    subscribers.add(subscriber);
    subscriber(getCategories(), false);
};

export const emit = (fromCookie: boolean) => {
    subscribers.forEach((subscriber) => subscriber(getCategories(), fromCookie));
}