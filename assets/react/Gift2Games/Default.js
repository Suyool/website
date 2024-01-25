import React, { useState, useEffect } from "react";

const Default = ({ categories, handleChildCategoryClick, desiredChildIdsMap }) => {
    // Convert category IDs to numbers
    const categoriesWithNumberIds = categories.map((category) => ({
        ...category,
        id: Number(category.id),
    }));

    // Extract all category IDs from the desiredChildIdsMap
    const categoryIdsToDisplay = Object.keys(desiredChildIdsMap).map(Number);

    // State to track the active category
    const [activeCategoryId, setActiveCategoryId] = useState(categoryIdsToDisplay[0]);

    // Effect to handle initial rendering
    useEffect(() => {
        // Set the first category as active on initial render
        setActiveCategoryId(categoryIdsToDisplay[0]);
    }, [categoryIdsToDisplay]);

    const handleCategoryClick = (categoryId) => {
        console.log(categoryId);
        // Set the clicked category as active
        setActiveCategoryId(categoryId);
    };

    return (
        <div id="Default_g2g">
            <div className="categories-scroll">
                {categoryIdsToDisplay.map((categoryId) => {
                    const categoryToDisplay = categoriesWithNumberIds.find(
                        (category) => category.id === Number(categoryId)
                    );

                    return (
                        categoryToDisplay && (
                            <div
                                key={categoryToDisplay.id}
                                className={`category-item ${activeCategoryId === Number(categoryId) ? "selected" : ""}`}
                                onClick={() => handleCategoryClick(Number(categoryId))}
                            >
                                <img src={categoryToDisplay.image} alt={categoryToDisplay.title} />
                                <p className="SubTitleCat">{categoryToDisplay.title}</p>
                            </div>
                        )
                    );
                })}
            </div>

            {/* Display child categories for the active category */}
            <div className="child-categories">
                {categoriesWithNumberIds
                    .find((category) => category.id === activeCategoryId)
                    ?.childs.map((child) => (
                        <div key={child.id} className="child-category">
                            <p className="SubTitleCat">{child.short_title}</p>
                        </div>
                    ))}
            </div>
        </div>
    );
};

export default Default;
