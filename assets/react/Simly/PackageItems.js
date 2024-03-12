import React, {useEffect, useState} from 'react';

const PackageItems = ({ country , setSelectedPlan,setActiveButton,setSelectedPackage}) => {
    // Function to handle click on a card
    const handleCardClick = (plan,packages) => {
        setSelectedPlan(plan); // Set the selected plan
        setSelectedPackage(packages)
        setActiveButton({name:"PackagesInfo"})
    };

    return (
        <div className="container itemsPackageCont">
            <div>
                <div className='subTitle2'>{country?.name}</div>
                <div className="row">
                    {country.plans.map((packageItem, index) => (
                        <div key={packageItem.planId} className="col-md-6">
                            <div
                                className={`card mb-3 bg-package${(index % 3) + 1}`}
                                onClick={() => handleCardClick(country,packageItem)} // Handle click event
                            >
                                <div className="card-body">
                                    <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                                        <div className="itemsList">
                                            <h6 className="card-title">
                                                <img src={country.countryImageURL} alt={country.name} width={17} />
                                                <span className="ms-2" style={{fontFamily:"PoppinsMedium"}}>{country?.name}</span>
                                            </h6>
                                            <p className="card-text itemSize">{packageItem.size}GB</p>
                                            {/* <p className="card-text desc">{packageItem.activationPolicy}</p> */}
                                            <p className="card-text desc">Valid for {packageItem.duration} Days</p>
                                        </div>
                                        <div>
                                            <p className="card-text price">${packageItem.initial_price}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    ))}
                </div>
            </div>
        </div>
    );
};

export default PackageItems;
