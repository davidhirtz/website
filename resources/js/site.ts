import Router from "skeleton-router";

const router = new Router();

router.afterRender = () => {
    console.log("Hello world");
};

router.afterRender();