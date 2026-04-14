import {Categories, categories} from "./categories.ts";

type Subscriber = (categories: Categories, fromCookie: boolean) => void;

const subscribers: Set<Subscriber> = new Set();

export const subscribe = (subscriber: Subscriber) => {
    subscribers.add(subscriber);
    subscriber(categories, false);
};

export const emit = (fromCookie: boolean) => {
    subscribers.forEach((subscriber) => subscriber(categories, fromCookie));
}